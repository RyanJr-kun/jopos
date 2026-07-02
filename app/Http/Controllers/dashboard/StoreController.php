<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\EmployeeProfile;
use App\Models\Store;
use App\Models\User; // Untuk tarik data PIC
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-toko-gudang', only: ['index', 'upload', 'revert', 'getMembers']),
            
            // 2. Akses Tambah Data
            new Middleware('permission:create-toko-gudang', only: ['store']),
            
            // 3. Akses Edit Data
            new Middleware('permission:edit-toko-gudang', only: ['update', 'updateMembers']),
            
            // 4. Akses Hapus Data
            new Middleware('permission:delete-toko-gudang', only: ['destroy']),
        ];
    }

   public function index(Request $request)
    {
        $query = Store::query()->with(['pic', 'employees']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_toko', 'LIKE', "%{$search}%")
                ->orWhere('alamat', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('daerah')) {
            $daerah = $request->daerah;
            $query->where(function ($q) use ($daerah) {
                $q->where('provinsi', 'LIKE', "%{$daerah}%")
                ->orWhere('kabupaten_kota', 'LIKE', "%{$daerah}%")
                ->orWhere('kecamatan', 'LIKE', "%{$daerah}%")
                ->orWhere('desa', 'LIKE', "%{$daerah}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'Aktif' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        $filteredStores = $query->latest()->get();

        $tokos = $filteredStores->filter(fn ($item) => strtolower($item->type) === 'toko')->values();
        $gudangs = $filteredStores->filter(fn ($item) => strtolower($item->type) === 'gudang')->values();

        // Response AJAX untuk live filter (tanpa reload / tombol submit)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'tokos_html'   => view('content.hrd.store._toko-cards', compact('tokos'))->render(),
                'gudangs_html' => view('content.hrd.store._gudang-cards', compact('gudangs'))->render(),
                'tokos_count'  => $tokos->count(),
                'gudangs_count' => $gudangs->count(),
                'total'        => $filteredStores->count(),
            ]);
        }

        $types = Store::getTypes();
        $employees = User::with('employee')->get(); // Sesuaikan relasimu

        return view('content.hrd.store.index', compact('tokos', 'gudangs', 'types', 'employees'));
    }
    /**
     * Menyimpan data toko/gudang baru.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_toko' => 'required|string|max:100',
            'type' => 'required|in:toko,gudang',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'map_url' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'pic_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validatedData['is_active'] = $request->has('is_active');

        // Handle FilePond Upload
        if (!empty($validatedData['logo'])) {
            $tempPath = $validatedData['logo'];
            if (str_starts_with($tempPath, 'tmp/') && Storage::disk('r2')->exists($tempPath)) {
                $newPath = 'profil-toko/' . basename($tempPath);
                Storage::disk('r2')->move($tempPath, $newPath);
                $validatedData['logo'] = $newPath;
            } else {
                $validatedData['logo'] = null;
            }
        }

        Store::create($validatedData);

        return redirect()->route('toko.index')->with('success', 'Lokasi baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data toko/gudang.
     */
    /**
     * Memperbarui data toko/gudang.
     */
    public function update(Request $request, Store $toko)
    {
        $validatedData = $request->validate([
            'name_toko' => 'required|string|max:100',
            'type' => 'required|in:toko,gudang',
            'provinsi' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'desa' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'map_url' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'pic_id' => 'nullable|exists:users,id',
            'logo' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validatedData['is_active'] = $request->has('is_active');

        if ($request->filled('logo')) {
        // Logika filepond upload (memindahkan dari tmp ke profil-toko)
        $tempPath = $request->input('logo');
        if (str_starts_with($tempPath, 'tmp/') && Storage::disk('r2')->exists($tempPath)) {
            $newPath = 'profil-toko/' . basename($tempPath);
            Storage::disk('r2')->move($tempPath, $newPath);

            if ($toko->logo && Storage::disk('r2')->exists($toko->logo)) {
                Storage::disk('r2')->delete($toko->logo);
            }
            $validatedData['logo'] = $newPath;
        }
    } else {
        // JIKA USER MENEKAN TOMBOL "HAPUS LOGO (X)" DAN TIDAK UPLOAD YANG BARU
        if ($request->input('remove_logo') == '1') {
            if ($toko->logo && Storage::disk('r2')->exists($toko->logo)) {
                Storage::disk('r2')->delete($toko->logo);
            }
            $validatedData['logo'] = null; // Set null di database
        } else {
            unset($validatedData['logo']); // Abaikan, biarkan logo lama tetap ada
        }
    }

        $toko->update($validatedData);

        return redirect()->route('toko.index')->with('success', 'Profil lokasi berhasil diperbarui.');
    }

    /**
     * Menghapus data toko/gudang.
     */
    public function destroy(Store $toko)
    {
        if ($toko->logo && Storage::disk('r2')->exists($toko->logo)) {
            Storage::disk('r2')->delete($toko->logo);
        }

        $toko->delete();

        return redirect()->route('toko.index')->with('success', 'Lokasi berhasil dihapus.');
    }

    public function upload(Request $request)
    {
        $inputName = $request->hasFile('logo') ? 'logo' : 'filepond';

        // Validasi input
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            $inputName => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            $errors = implode(', ', $validator->errors()->all());
            return response($errors, 422)->header('Content-Type', 'text/plain');
        }

        // Simpan file
        if ($request->hasFile($inputName)) {
            $file = $request->file($inputName);
            $path = $file->store('tmp/profil-toko', 'r2');

            return response($path, 200)->header('Content-Type', 'text/plain');
        }

        return response('Gagal menemukan file gambar.', 400)->header('Content-Type', 'text/plain');
    }

    /**
     * Menghapus file yang diunggah sementara oleh FilePond.
     */
    public function revert(Request $request)
    {
        $filePath = $request->getContent();

        if (str_starts_with(trim($filePath), '<!DOCTYPE')) {
            return response()->json(['error' => 'Invalid path provided.'], 400);
        }

        if ($filePath && Storage::disk('r2')->exists($filePath) && str_starts_with($filePath, 'tmp/')) {
            Storage::disk('r2')->delete($filePath);
            return response()->noContent();
        }
        return response()->json(['error' => 'File not found.'], 404); 
    }

    /**
     * Mengambil daftar semua karyawan dan status keanggotaan di toko tertentu (AJAX).
     */
    /**
     * Mengambil daftar semua karyawan dan status keanggotaan di toko tertentu (AJAX).
     * Tambahkan ?members_only=1 untuk dapat payload ringan (khusus anggota toko ini saja),
     * dipakai oleh popover detail anggota di kartu toko/gudang.
     */
    public function getMembers(Request $request, $id)
    {
        $store = Store::findOrFail($id);

        // Ambil semua user yang memiliki profil karyawan
        $allEmployees = User::has('employee')
            ->with('employee.store')
            ->get()
            ->map(function ($user) use ($id) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'jabatan' => $user->employee->jabatan ?? '-',
                    'avatar' => $user->employee->avatar ? Storage::disk('r2')->url($user->employee->avatar) : null,
                    'is_member' => $user->employee->store_id == $id,
                    'current_store' => $user->employee->store?->name_toko ?? 'Belum Ditempatkan'
                ];
            });

        if ($request->boolean('members_only')) {
            $allEmployees = $allEmployees->where('is_member', true)->values();
        }

        return response()->json([
            'store_name' => $store->name_toko,
            'employees' => $allEmployees
        ]);
    }

    /**
     * Memperbarui penempatan karyawan ke toko ini.
     */
    public function updateMembers(Request $request, Store $toko)
    {
        $selectedUserIds = $request->input('user_ids', []);

        EmployeeProfile::query()->whereIn('user_id', $selectedUserIds)
            ->update(['store_id' => $toko->id]);

        EmployeeProfile::query()->where('store_id', $toko->id)
            ->whereNotIn('user_id', $selectedUserIds)
            ->update(['store_id' => null]);

        return response()->json(['success' => true, 'message' => 'Daftar anggota berhasil diperbarui.']);
    }
}