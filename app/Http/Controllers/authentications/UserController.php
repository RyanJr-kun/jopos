<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles')->orderBy('id', 'DESC');

        // filter pencarian name, nidn, username.
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('nidn', 'LIKE', "%{$search}%")
                    ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        //filter select role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $data = $query->get(); // ambil semua data query
        $roles = Role::pluck('name', 'name')->all();

        return view('content.user.index', compact('data', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('content.user.create', [
            'roles' => Role::all()
        ]);
    }

    /**
     * nyetor data baru ke penyimpanan.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'username' => 'required|min:3|max:255|unique:users',
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:5|max:255',
            'role_id' => ['required', Rule::exists('roles', 'id')],
            'kontak' => 'nullable|min:9|max:14|unique:users',
            'mulai_kerja' => 'required|date',
            'status' => 'required|boolean',
            'img_user' => 'nullable|string', // Diubah dari 'image' menjadi 'string'
        ]);

        // Pindahkan gambar dari temp ke folder user-images
        if ($request->img_user) {
            $tempPath = $request->img_user;
            // Pastikan file ada di folder temporary
            if (Storage::disk('public')->exists($tempPath)) {
                // Buat path baru dan pindahkan file
                $newPath = str_replace('tmp/user-images/', 'user-images/', $tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $validatedData['img_user'] = $newPath;
            }
        }

        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);
        return redirect()->route('users.index')->with('success', 'User Baru Berhasil Ditambahkan.');
    }

    /**
     * Display the specified resource. iki durung kangge bjir
     */
    public function show(user $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('content.user.edit', [
            'user' => $user,
            'roles' => Role::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Cek jika pengguna yang sedang login mencoba mengubah role-nya sendiri
        if (Auth::id() === $user->id && $request->input('role_id') != $user->role_id) {
            return back()->withInput()->with('warning', 'Anda tidak dapat mengubah role Anda sendiri.');
        }

        $rules = [
            'name' => 'required|max:255',
            'username' => ['required', 'min:3', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email:dns', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:5|max:255',
            'role_id' => ['required', Rule::exists('roles', 'id')],
            'kontak' => ['nullable', 'min:9', 'max:14', Rule::unique('users')->ignore($user->id)],
            'mulai_kerja' => 'required|date',
            'status' => 'required|boolean',
            'img_user' => 'nullable|string',
        ];

        $validatedData = $request->validate($rules);

        // Cek apakah ada gambar baru yang diunggah (path dimulai dengan 'tmp/')
        if ($request->filled('img_user') && str_starts_with($request->img_user, 'tmp/')) {
            $tempPath = $request->img_user;
            if (Storage::disk('public')->exists($tempPath)) {
                // Hapus gambar lama jika ada
                if ($user->img_user && Storage::disk('public')->exists($user->img_user)) {
                    Storage::disk('public')->delete($user->img_user);
                }
                // Pindahkan gambar baru dari tmp ke folder user-images
                $newPath = str_replace('tmp/user-images/', 'user-images/', $tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $validatedData['img_user'] = $newPath;
            }
            // Cek jika pengguna menghapus gambar (input ada tapi nilainya kosong/null)
        } elseif ($request->exists('img_user') && $request->input('img_user') === null) {
            if ($user->img_user && Storage::disk('public')->exists($user->img_user)) {
                Storage::disk('public')->delete($user->img_user);
                $validatedData['img_user'] = null;
            }
        } else {
            // Jika tidak ada perubahan gambar, hapus dari data yang divalidasi agar tidak menimpa path yang ada
            unset($validatedData['img_user']);
        }

        if ($request->filled('password')) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }
        $user->update($validatedData);
        return redirect()->route('users.index')->with('success', 'Data Pengguna Berhasil Diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->img_user) {
            Storage::disk('public')->delete($user->img_user);
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Data Pengguna Berhasil Dihapus.');
    }

    /**
     * Menangani unggahan file asinkron dari FilePond.
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('img_user')) {
            $request->validate([
                'img_user' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            ]);
            $file = $request->file('img_user');
            // Simpan ke storage/app/public/tmp/user-images
            $path = $file->store('tmp/user-images', 'public');
            // Kembalikan path sebagai response text, FilePond akan menangkap ini
            return $path;
        }
        // Jika gagal
        return response('Gagal mengunggah.', 500);
    }

    /**
     * Menangani pembatalan unggahan file dari FilePond.
     */
    public function revert(Request $request)
    {
        // FilePond mengirimkan path file sebagai konten body request
        $filePath = $request->getContent();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->noContent(); // Berhasil, tidak ada konten untuk dikembalikan
        }

        return response()->json(['error' => 'File not found or path is missing.'], 404);
    }
}
