<?php

namespace App\Http\Controllers\event;

use App\Http\Controllers\Controller;
use App\Enums\BannerPosition;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rules\Enum;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('content.banner.index', [
            'title' => 'Manajemen Banner',
            'banners' => Banner::orderBy('posisi')->orderBy('urutan')->get(),
            'positions' => BannerPosition::cases(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'img_banner' => 'required|string|starts_with:tmp/',
            'judul' => 'nullable|string|max:255',
            'url_tujuan' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
            'posisi' => ['required', new Enum(BannerPosition::class)],
            'urutan' => 'required|integer|min:0',
        ]);

        // Jika ada file yang diunggah melalui FilePond
        if ($request->filled('img_banner')) {
            $sourcePath = $request->input('img_banner'); // Path dari folder tmp
            $fileName = basename($sourcePath);
            $destinationPath = 'banners/' . $fileName;

            // Pindahkan file dari tmp ke direktori tujuan
            if (Storage::disk('public')->exists($sourcePath)) {
                Storage::disk('public')->move($sourcePath, $destinationPath);
                $validatedData['img_banner'] = $destinationPath; // Simpan path baru
            } else {
                // Hapus path jika file tidak ditemukan untuk mencegah error
                unset($validatedData['img_banner']);
            }
        }


        $validatedData['is_active'] = $request->has('is_active');

        $banner = Banner::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Banner baru berhasil ditambahkan.',
            'data'    => $banner
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $validatedData = $request->validate([
            'judul' => 'nullable|string|max:255',
            'url_tujuan' => 'nullable|url|max:255',
            'img_banner' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'posisi' => ['required', new Enum(BannerPosition::class)],
            'urutan' => 'required|integer|min:0',
        ]);

        // Cek apakah ada file baru yang diunggah
        if ($request->filled('img_banner')) {
            $sourcePath = $request->input('img_banner');

            // Pastikan ini adalah file baru dari tmp, bukan path file lama
            if (strpos($sourcePath, 'tmp/') === 0 && Storage::disk('public')->exists($sourcePath)) {
                // Hapus gambar lama jika ada
                if ($banner->img_banner) {
                    Storage::disk('public')->delete($banner->img_banner);
                }

                // Pindahkan gambar baru dari tmp ke lokasi permanen
                $fileName = basename($sourcePath);
                $destinationPath = 'banners/' . $fileName;
                Storage::disk('public')->move($sourcePath, $destinationPath);
                $validatedData['img_banner'] = $destinationPath;
            }
            // Menangani kasus jika pengguna menghapus gambar yang ada melalui FilePond
        } elseif ($request->input('img_banner') === null) {
            if ($banner->img_banner && Storage::disk('public')->exists($banner->img_banner)) {
                Storage::disk('public')->delete($banner->img_banner);
                $validatedData['img_banner'] = null;
            }
        }

        $validatedData['is_active'] = $request->has('is_active');
        $banner->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Data banner berhasil diperbarui.',
            'data' => $banner
        ]);
    }

    public function destroy(Banner $banner)
    {
        if ($banner->img_banner && Storage::disk('public')->exists($banner->img_banner)) {
            Storage::disk('public')->delete($banner->img_banner);
        }
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner berhasil dihapus.'
        ]);
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('img_banner')) {
            $request->validate([
                'img_banner' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            ]);
            $file = $request->file('img_banner');
            $path = $file->store('tmp/banners', 'public');
            return $path;
        }
        return response('Gagal mengunggah.', 500);
    }

    public function revert(Request $request)
    {
        $filePath = $request->getContent();
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->noContent();
        }
        return response()->json(['error' => 'File not found.'], 404);
    }

    public function getJson(Banner $banner)
    {
        return response()->json($banner);
    }
}
