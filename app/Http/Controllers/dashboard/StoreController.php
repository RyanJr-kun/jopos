<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\EmployeeProfile;
use App\Models\User; // Untuk tarik data PIC
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    public function index()
    {
        // Kita pisahkan datanya di sini agar di Blade tinggal pakai Tabs
        $tokos = Store::toko()->latest()->get();
        $gudangs = Store::gudang()->latest()->get();

        // Ambil user untuk dropdown pilihan Kepala Toko (PIC) saat Create/Edit
        $users = User::all();

        return view('content.hrd.store.index', compact('tokos', 'gudangs', 'users'));
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
            'is_active' => 'boolean',
        ]);

        // Default is_active = true jika tidak dikirim dari form
        $validatedData['is_active'] = $request->has('is_active') ? true : false;

        // Handle FilePond Upload
        if ($request->filled('logo')) {
            $tempPath = $request->input('logo');
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = 'profil-toko/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
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
        ]);

        $validatedData['is_active'] = $request->has('is_active') ? true : false;

        // Handle FilePond
        if ($request->filled('logo')) {
            $tempPath = $request->input('logo');
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = 'profil-toko/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);

                // Hapus logo lama jika ada
                if ($toko->logo && Storage::disk('public')->exists($toko->logo)) {   
                    Storage::disk('public')->delete($toko->logo);   
                }
                $validatedData['logo'] = $newPath;
            }
        } else {
            // Jika form logo kosong (artinya dihapus)
            if ($toko->logo && Storage::disk('public')->exists($toko->logo)) {   
                Storage::disk('public')->delete($toko->logo);   
            }
            $validatedData['logo'] = null;
        }

        $toko->update($validatedData);   

        return redirect()->route('toko.index')->with('success', 'Profil lokasi berhasil diperbarui.');
    }

    /**
     * Menyimpan file yang diunggah sementara oleh FilePond.
     */
    /**
     * Menyimpan file yang diunggah sementara oleh FilePond.
     */
    public function upload(Request $request)
    {
        // Deteksi nama field otomatis (apakah FilePond mengirim 'logo' atau 'filepond')
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
            $path = $file->store('tmp/profil-toko', 'public');

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

        // Tambahkan pengecekan untuk memastikan filePath bukan HTML
        if (str_starts_with(trim($filePath), '<!DOCTYPE')) {
            return response()->json(['error' => 'Invalid path provided.'], 400);
        }

        if ($filePath && Storage::disk('public')->exists($filePath) && str_starts_with($filePath, 'tmp/')) {
            Storage::disk('public')->delete($filePath);
            return response()->noContent();
        }
        return response()->json(['error' => 'File not found.'], 404); // Tetap JSON untuk error
    }
    // Tambahkan di App\Http\Controllers\dashboard\StoreController.php

/**
 * Mengambil daftar semua karyawan dan status keanggotaan di toko tertentu (AJAX).
 */
    public function getMembers($id)
    {
        $store = Store::findOrFail($id);
        
        // Ambil semua user yang memiliki profil karyawan
        $allEmployees = User::has('profile')
            ->with('profile.store')
            ->get()
            ->map(function($user) use ($id) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'jabatan' => $user->profile->jabatan ?? '-',
                    'is_member' => $user->profile->store_id == $id,
                    'current_store' => $user->profile->store->name_toko ?? 'Belum Ditempatkan'
                ];
            });

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

        EmployeeProfile::whereIn('user_id', $selectedUserIds)
            ->update(['store_id' => $toko->id]);

        EmployeeProfile::where('store_id', $toko->id)
            ->whereNotIn('user_id', $selectedUserIds)
            ->update(['store_id' => null]);

        return response()->json(['success' => true, 'message' => 'Daftar anggota berhasil diperbarui.']);
    }
}
