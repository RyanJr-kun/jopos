<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Store;
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
            'logo' => 'nullable|string', // Path dari FilePond
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

        return redirect()->route('store.index')->with('success', 'Lokasi baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data toko/gudang.
     */
    public function update(Request $request, Store $store)
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
                if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                    Storage::disk('public')->delete($store->logo);
                }
                $validatedData['logo'] = $newPath;
            }
        } else {
            // Jika form logo kosong (artinya dihapus)
            if ($store->logo && Storage::disk('public')->exists($store->logo)) {
                Storage::disk('public')->delete($store->logo);
            }
            $validatedData['logo'] = null;
        }

        $store->update($validatedData);

        return redirect()->route('store.index')->with('success', 'Profil lokasi berhasil diperbarui.');
    }

    /**
     * Menyimpan file yang diunggah sementara oleh FilePond.
     */
    public function upload(Request $request)
    {
        // Validasi input terlebih dahulu
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
        ]);

        // === PERBAIKAN DI SINI ===
        if ($validator->fails()) {
            // Gabungkan pesan error menjadi satu string
            $errors = implode(', ', $validator->errors()->all());
            // Kembalikan sebagai plain text dengan status error yang sesuai
            return response($errors, 422)->header('Content-Type', 'text/plain');
        }

        // Jika validasi berhasil dan file ada
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            // Simpan file ke direktori 'tmp' di dalam 'storage/app/public'
            $path = $file->store('tmp/profil-toko', 'public');

            // Kembalikan path file sebagai plain text, bukan JSON
            return response($path, 200)->header('Content-Type', 'text/plain');
        }

        // Jika tidak ada file, kembalikan response error
        return response()->json(['error' => 'No file uploaded.'], 400);
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
}
