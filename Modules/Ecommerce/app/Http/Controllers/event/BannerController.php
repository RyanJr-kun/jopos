<?php

namespace Modules\Ecommerce\Http\Controllers\event;

use App\Http\Controllers\Controller;
use App\Enums\BannerPosition;
use Modules\Ecommerce\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Enum;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::orderBy('posisi')->orderBy('urutan')->get();
        $positions = BannerPosition::cases();
        return view('ecommerce::banner.index', compact('banners', 'positions'));
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

        if (!empty($validatedData['img_banner'])) {
            $sourcePath = $validatedData['img_banner'];
            $fileName = basename($sourcePath);
            $destinationPath = 'banners/' . $fileName;

            try {
                Storage::disk('r2')->move($sourcePath, $destinationPath);
                $validatedData['img_banner'] = $destinationPath; 
            } catch (\Exception $e) {
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

        if ($request->filled('img_banner') && str_starts_with($request->img_banner, 'tmp/')) {
            // 1. Gambar baru diunggah dari komputer
            $sourcePath = $request->img_banner;
            $fileName = basename($sourcePath);
            $destinationPath = 'banners/' . $fileName;

            try {
                // Hapus yang lama dulu
                if ($banner->img_banner) {
                    try { Storage::disk('r2')->delete($banner->img_banner); } catch (\Exception $e) {}
                }
                // Pindah yang baru
                Storage::disk('r2')->move($sourcePath, $destinationPath);
                $validatedData['img_banner'] = $destinationPath;
            } catch (\Exception $e) {
                // Biarkan jika gagal
            }

        } elseif ($request->input('remove_banner') == '1') {
            // 2. Tombol X ditekan
            if ($banner->img_banner) {
                try { Storage::disk('r2')->delete($banner->img_banner); } catch (\Exception $e) {}
            }
            $validatedData['img_banner'] = null; // Kosongkan field di database

        } else {
            // 3. Tidak ada aksi pada gambar (filepond tidak disentuh, tombol X tidak ditekan)
            unset($validatedData['img_banner']);
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
        if ($banner->img_banner) {
            try { Storage::disk('r2')->delete($banner->img_banner); } catch (\Exception $e) {}
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
            $path = $file->store('tmp/banners', 'r2');
            return $path;
        }
        return response('Gagal mengunggah.', 500);
    }

    public function revert(Request $request)
    {
        $filePath = $request->getContent();
        if ($filePath) {
            try {
                Storage::disk('r2')->delete($filePath);
                return response()->noContent();
            } catch (\Exception $e) {
                // Abaikan error (bohongi Filepond agar reset UI)
                return response()->noContent();
            }
        }
        return response()->json(['error' => 'File not found.'], 404);
    }

    public function getJson(Banner $banner)
    {
        return response()->json($banner);
    }
}
