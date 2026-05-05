<?php

namespace App\Http\Controllers\hrd;

use App\Enums\Jabatan;
use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'employee.store'])->orderBy('id', 'DESC');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('status')) {
            $status = $request->status === 'Aktif' ? 1 : 0;
            $query->where('status', $status);
        }

        $data  = $query->get();
        $roles = Role::pluck('name', 'name')->all();

        if ($request->ajax()) {
            $html = view('content.hrd.user.index', compact('data', 'roles'))->fragment('user-table-body');
            return response()->json(['html' => $html, 'total' => $data->count()]);
        }

        return view('content.hrd.user.index', compact('data', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.hrd.user.create', [
            'roles'  => Role::all(),
            'stores' => Store::where('is_active', true)->orderBy('name_toko')->get(),
            'jabatans' => Jabatan::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * Membuat user baru sekaligus employee_profile-nya dalam satu transaksi.
     */
    public function store(Request $request)
    {
        // --- Validasi ---
        $validated = $request->validate([
            // Data users
            'name'          => 'required|max:255',
            'username'      => 'required|min:3|max:100|unique:users',
            'email'         => 'required|email:dns|unique:users',
            'password'      => 'required|min:5|max:255',
            'status'        => 'required|boolean',
            'role_name'     => ['required', Rule::exists('roles', 'name')],

            // Data employee_profiles
            'store_id'          => 'nullable|exists:stores,id',
            'kontak'            => 'nullable|min:9|max:20',
            'alamat'            => 'nullable|string|max:500',
            'jabatan'           => ['nullable', Rule::enum(Jabatan::class)],
            'nik'               => 'nullable|string|max:50|unique:employee_profiles,nik',
            'tanggal_bergabung' => 'nullable|date',
            'avatar'            => 'nullable|string',   // path tmp dari FilePond
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Pindahkan avatar dari folder tmp ke folder permanen
            $avatarPath = null;
            if (!empty($validated['avatar'])) {
                $tempPath = $validated['avatar'];
                if (Storage::disk('public')->exists($tempPath)) {
                    $newPath = str_replace('tmp/user-images/', 'user-images/', $tempPath);
                    Storage::disk('public')->move($tempPath, $newPath);
                    $avatarPath = $newPath;
                }
            }

            // 2. Buat user
            $user = User::create([
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'email'    => $validated['email'],
                'password' => bcrypt($validated['password']),
                'status'   => $validated['status'],
            ]);

            // 3. Assign role (Spatie)
            $user->syncRoles($validated['role_name']);

            // 4. Buat employee_profile
            EmployeeProfile::create([
                'user_id'           => $user->id,
                'store_id'          => $validated['store_id'] ?? null,
                'kontak'            => $validated['kontak'] ?? null,
                'alamat'            => $validated['alamat'] ?? null,
                'jabatan'           => $validated['jabatan'] ?? null,
                'nik'               => $validated['nik'] ?? null,
                'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? null,
                'avatar'            => $avatarPath,
            ]);
        });

        return redirect()->route('users.index')->with('success', 'User Baru Berhasil Ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['roles', 'employee.store']);
        return view('content.hrd.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(User $user)
    {
        // Load relasi profil dan toko KHUSUS untuk user ini saja
        $user->load('employee.store');

        return view('content.hrd.user.edit', [
            'user'     => $user,
            'roles'    => Role::all(),
            'stores'   => Store::where('is_active', true)->orderBy('name_toko')->get(),
            'jabatans' => Jabatan::cases(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Cegah user mengubah role-nya sendiri
        if (Auth::id() === $user->id && $request->role_name !== $user->getRoleNames()->first()) {
            return back()->withInput()->with('warning', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $validated = $request->validate([
            // Data users
            'name'     => 'required|max:255',
            'username' => ['required', 'min:3', 'max:100', Rule::unique('users')->ignore($user->id)],
            'email'    => ['required', 'email:dns', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:5|max:255',
            'status'   => 'required|boolean',
            'role_name'=> ['required', Rule::exists('roles', 'name')],

            // Data employee_profiles
            'store_id'          => 'nullable|exists:stores,id',
            'kontak'            => 'nullable|min:9|max:20',
            'alamat'            => 'nullable|string|max:500',
            'jabatan'           => ['nullable', Rule::enum(Jabatan::class)],
            'nik'               => ['nullable', 'string', 'max:50', Rule::unique('employee_profiles', 'nik')->ignore($user->employee?->id)],
            'tanggal_bergabung' => 'nullable|date',
            'avatar'            => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $request, $user) {
            $profile = $user->employee ?? new EmployeeProfile(['user_id' => $user->id]);

            // --- Tangani Avatar ---
            if ($request->filled('avatar') && str_starts_with($request->avatar, 'tmp/')) {
                // Ada gambar baru dari FilePond
                $tempPath = $request->avatar;
                if (Storage::disk('public')->exists($tempPath)) {
                    // Hapus avatar lama
                    if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                        Storage::disk('public')->delete($profile->avatar);
                    }
                    $newPath = str_replace('tmp/user-images/', 'user-images/', $tempPath);
                    Storage::disk('public')->move($tempPath, $newPath);
                    $validated['avatar'] = $newPath;
                }
            } elseif ($request->exists('avatar') && $request->input('avatar') === null) {
                // Pengguna menghapus avatar
                if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                    Storage::disk('public')->delete($profile->avatar);
                }
                $validated['avatar'] = null;
            } else {
                // Tidak ada perubahan avatar — jangan timpa path yang ada
                unset($validated['avatar']);
            }

            // --- Update tabel users ---
            $userData = [
                'name'     => $validated['name'],
                'username' => $validated['username'],
                'email'    => $validated['email'],
                'status'   => $validated['status'],
            ];
            if (!empty($validated['password'])) {
                $userData['password'] = bcrypt($validated['password']);
            }
            $user->update($userData);

            // --- Update role ---
            $user->syncRoles($validated['role_name']);

            // --- Update employee_profile ---
            $profileData = [
                'user_id'           => $user->id,
                'store_id'          => $validated['store_id'] ?? null,
                'kontak'            => $validated['kontak'] ?? null,
                'alamat'            => $validated['alamat'] ?? null,
                'jabatan'           => $validated['jabatan'] ?? null,
                'nik'               => $validated['nik'] ?? null,
                'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? null,
            ];
            if (isset($validated['avatar'])) {
                $profileData['avatar'] = $validated['avatar'];
            }
            $profile->fill($profileData)->save();
        });

        return redirect()->route('users.index')->with('success', 'Data Pengguna Berhasil Diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            // Hapus avatar dari storage jika ada
            if ($user->employee?->avatar) {
                Storage::disk('public')->delete($user->employee->avatar);
            }
            // employee_profile akan terhapus otomatis jika ada cascade di migration,
            // jika tidak, hapus manual:
            $user->employee?->delete();
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'Data Pengguna Berhasil Dihapus.');
    }

    /**
     * Menangani unggahan file asinkron dari FilePond.
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);
            $path = $request->file('avatar')->store('tmp/user-images', 'public');
            return $path;
        }
        return response('Gagal mengunggah.', 500);
    }

    /**
     * Menangani pembatalan unggahan file dari FilePond.
     */
    public function revert(Request $request)
    {
        $filePath = $request->getContent();
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->noContent();
        }
        return response()->json(['error' => 'File not found.'], 404);
    }
}