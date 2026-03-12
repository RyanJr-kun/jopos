<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use \Cviebrock\EloquentSluggable\Services\SlugService;


class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statuses = Brand::select('status')->distinct()->pluck('status');
        $query = Brand::withCount('products')->latest();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }
        if ($request->filled('status')) {
            $statusValue = $request->input('status') === 'Aktif' ? 1 : 0;
            $query->where('status', $statusValue);
        }

        $brands = $query->paginate(15)->withQueryString();
        if ($request->ajax()) {
            return view('content.produk._brand_table', compact('brands'))->render();
        }

        return view('content.produk.brand', [
            'title' => 'Data Brand',
            'brands' => $brands,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // app/Http/Controllers/BrandController.php

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'img_brand' => 'nullable|string|starts_with:tmp/',
            'name' => 'required|max:255|unique:brands',
            'slug' => 'required|max:255|unique:brands',
            'status' => 'nullable|boolean',
        ]);

        // Jika ada file yang diunggah melalui FilePond
        if ($request->filled('img_brand')) {
            $sourcePath = $request->input('img_brand'); // Path dari folder tmp
            $fileName = basename($sourcePath);
            $destinationPath = 'brand-images/' . $fileName;

            // Pindahkan file dari tmp ke direktori tujuan
            if (Storage::disk('public')->exists($sourcePath)) {
                Storage::disk('public')->move($sourcePath, $destinationPath);
                $validatedData['img_brand'] = $destinationPath; // Simpan path baru
            } else {
                // Hapus path jika file tidak ditemukan untuk mencegah error
                unset($validatedData['img_brand']);
            }
        }

        $validatedData['status'] = $request->has('status');
        $brand = Brand::create($validatedData);

        // Cek jika request adalah AJAX
        if ($request->wantsJson()) {
            // Muat relasi dan format tanggal untuk konsistensi
            $brand->loadCount('products');
            $brand->created_at_formatted = $brand->created_at->translatedFormat('d M Y');

            return response()->json([
                'success' => true,
                'message' => 'Brand baru berhasil ditambahkan.',
                'data'    => $brand
            ], 201);
        }

        // Respons standar jika bukan AJAX
        Alert::success('Berhasil', 'Brand Baru Berhasil Ditambahkan.');
        return redirect()->route('brand.index');
    }
    public function show(Brand $brand)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        //
    }

    public function getBrandJson(Brand $brand)
    {
        return response()->json($brand);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, Brand $brand)
    {
        $rules = [
            'img_brand' => 'nullable|string',
            'name' => ['required', 'max:255', Rule::unique('brands')->ignore($brand->id)],
            'slug' => ['required', 'max:255', Rule::unique('brands')->ignore($brand->id)],
            'status' => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);

        // Cek apakah ada file baru yang diunggah
        if ($request->filled('img_brand')) {
            $sourcePath = $request->input('img_brand');

            // Pastikan ini adalah file baru dari tmp, bukan path file lama
            if (strpos($sourcePath, 'tmp/') === 0 && Storage::disk('public')->exists($sourcePath)) {
                // Hapus gambar lama jika ada
                if ($brand->img_brand) {
                    Storage::disk('public')->delete($brand->img_brand);
                }

                // Pindahkan gambar baru dari tmp ke lokasi permanen
                $fileName = basename($sourcePath);
                $destinationPath = 'brand-images/' . $fileName;
                Storage::disk('public')->move($sourcePath, $destinationPath);
                $validatedData['img_brand'] = $destinationPath;
            }
            // Menangani kasus jika pengguna menghapus gambar yang ada melalui FilePond
        } elseif ($request->exists('img_brand') && $request->input('img_brand') === null) {
            if ($brand->img_brand && Storage::disk('public')->exists($brand->img_brand)) {
                Storage::disk('public')->delete($brand->img_brand);
                $validatedData['img_brand'] = null;
            }
        }

        $validatedData['status'] = $request->has('status');
        $brand->update($validatedData);

        if ($request->wantsJson()) {
            $brand->loadCount('products');
            $brand->created_at_formatted = $brand->created_at->translatedFormat('d M Y');

            return response()->json([
                'success' => true,
                'message' => 'Data Brand Berhasil Diperbarui.',
                'data'    => $brand
            ]);
        }

        Alert::success('Berhasil', 'Data Brand Berhasil Diperbarui.');
        return redirect()->route('brand.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            $message = 'Brand tidak dapat dihapus karena masih memiliki produk terkait!';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            Alert::error('Gagal', $message);
            return back();
        }

        if ($brand->img_brand) {
            Storage::disk('public')->delete($brand->img_brand);
        }

        $brand->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data Brand Berhasil Dihapus.']);
        }

        Alert::success('Berhasil', 'Data Brand Berhasil Dihapus.');
        return redirect()->route('brand.index');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(Brand::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }

    /**
     * Menangani unggahan file sementara dari FilePond.
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('img_brand')) {
            $request->validate([
                'img_brand' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            ]);

            $file = $request->file('img_brand');
            // Simpan file ke direktori 'tmp' di dalam 'storage/app/public'
            $path = $file->store('tmp/brand-images', 'public');

            // Kembalikan path file sebagai plain text
            return $path;
        }

        // Jika tidak ada file, kembalikan response error
        return response()->json(['error' => 'No file uploaded.'], 400);
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
