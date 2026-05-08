<?php

namespace Modules\Inventory\Http\Controllers\master;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
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
            return view('inventory::master.brand._brand_table', compact('brands'))->render();
        }

        return view('inventory::master.brand.brand', compact('brands', 'statuses'));
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

        if ($request->filled('img_brand')) {
            $sourcePath = $request->input('img_brand');
            $fileName = basename($sourcePath);
            $destinationPath = 'brand-images/' . $fileName;

            if (Storage::disk('r2')->exists($sourcePath)) {
                Storage::disk('r2')->move($sourcePath, $destinationPath);
                $validatedData['img_brand'] = $destinationPath;
            } else {
                unset($validatedData['img_brand']);
            }
        }

        $validatedData['status'] = $request->has('status');
        Brand::create($validatedData);

        // Langsung redirect dengan pesan sukses (metode biasa)
        return redirect()->route('brand.index')->with('success', 'Brand Baru Berhasil Ditambahkan.');
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

        if ($request->filled('img_brand')) {
            $sourcePath = $request->input('img_brand');

            if (strpos($sourcePath, 'tmp/') === 0 && Storage::disk('r2')->exists($sourcePath)) {
                if ($brand->img_brand) {
                    Storage::disk('r2')->delete($brand->img_brand);
                }

                $fileName = basename($sourcePath);
                $destinationPath = 'brand-images/' . $fileName;
                Storage::disk('r2')->move($sourcePath, $destinationPath);
                $validatedData['img_brand'] = $destinationPath;
            }
        } elseif ($request->exists('img_brand') && $request->input('img_brand') === null) {
            if ($brand->img_brand && Storage::disk('r2')->exists($brand->img_brand)) {
                Storage::disk('r2')->delete($brand->img_brand);
                $validatedData['img_brand'] = null;
            }
        }

        $validatedData['status'] = $request->has('status');
        $brand->update($validatedData);

        // Langsung redirect dengan pesan sukses (metode biasa)
        return redirect()->route('brand.index')->with('success', 'Data Brand Berhasil Diperbarui.');
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
            return back()->with('error', $message);
        }

        if ($brand->img_brand) {
            Storage::disk('r2')->delete($brand->img_brand);
        }

        $brand->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data Brand Berhasil Dihapus.']);
        }

        return redirect()->route('brand.index')->with('success', 'Data Brand Berhasil Dihapus.');
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
            $path = $file->store('tmp/brand-images', 'r2');

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
        if ($filePath && Storage::disk('r2')->exists($filePath)) {
            Storage::disk('r2')->delete($filePath);
            return response()->noContent();
        }

        return response()->json(['error' => 'File not found.'], 404);
    }
}
