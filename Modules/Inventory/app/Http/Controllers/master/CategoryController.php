<?php

namespace Modules\Inventory\Http\Controllers\master;

use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Modules\Inventory\Models\Category;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-kategoriproduk', only: ['index', 'getKategoriJson', 'getParentOptions', 'chekSlug', 'upload', 'revert']), 
            new Middleware('permission:create-kategoriproduk', only: ['create', 'store']),
            new Middleware('permission:edit-kategoriproduk', only: ['edit', 'update']),
            new Middleware('permission:delete-kategoriproduk', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        // Ambil semua kategori utama untuk dropdown parent (dipakai di modal create/edit)
        $parentKategoris = Category::whereNull('parent_id')->latest()->get(['id', 'name']);

        $query = Category::with('parent', 'children')
            ->withCount('products')
            ->latest();

        $type = $request->input('type', 'utama');

        if ($type === 'utama') {
            $query->whereNull('parent_id');
        } elseif ($type === 'sub') {
            $query->whereNotNull('parent_id');
        }

        // Filter: hanya tampilkan kategori utama atau sub kategori berdasarkan tab
        if ($request->filled('type')) {
            if ($request->input('type') === 'utama') {
                $query->whereNull('parent_id');
            } elseif ($request->input('type') === 'sub') {
                $query->whereNotNull('parent_id');
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $statusValue = $request->input('status') === 'Aktif' ? 1 : 0;
            $query->where('status', $statusValue);
        }

        $kategoris = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('inventory::master.kategori._category_table', compact('kategoris'))->render();
        }

        return view('inventory::master.kategori.kategoriproduk', compact('kategoris', 'parentKategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'parent_id'    => 'nullable|exists:categories,id',
            'img_kategori' => 'nullable|string|starts_with:tmp/',
            'name'         => 'required|max:255|unique:categories',
            'slug'         => 'required|max:255|unique:categories',
            'status'       => 'nullable|boolean',
        ]);

        // Pindahkan file dari tmp ke direktori permanen
        if ($request->filled('img_kategori')) {
            $sourcePath = $request->input('img_kategori');
            $fileName   = basename($sourcePath);
            $destinationPath = 'kategori-images/' . $fileName;

            if (Storage::disk('r2')->exists($sourcePath)) {
                Storage::disk('r2')->move($sourcePath, $destinationPath);
                $validatedData['img_kategori'] = $destinationPath;
            } else {
                unset($validatedData['img_kategori']);
            }
        }

        $validatedData['status'] = $request->has('status');

        // parent_id null jika tidak diisi (kategori utama)
        $validatedData['parent_id'] = $request->filled('parent_id') ? $request->input('parent_id') : null;

        Category::create($validatedData);

        return redirect()
            ->route('kategoriproduk.index', ['type' => $request->input('category_type_create', 'utama')])
            ->with('success', 'Kategori Baru Berhasil Ditambahkan.');
    }

    /**
     * Mengembalikan data JSON untuk modal edit.
     */
    public function getKategoriJson(Category $kategoriproduk)
    {
        $kategoriproduk->load('parent:id,name');
        return response()->json($kategoriproduk);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $kategoriproduk)
    {
        $rules = [
            'parent_id'    => [
                'nullable',
                'exists:categories,id',
                // Mencegah kategori menjadi anak dari dirinya sendiri
                function ($attribute, $value, $fail) use ($kategoriproduk) {
                    if ($value == $kategoriproduk->id) {
                        $fail('Kategori tidak dapat menjadi sub kategori dari dirinya sendiri.');
                    }
                },
            ],
            'img_kategori' => 'nullable|string',
            'name'         => ['required', 'max:255', Rule::unique('categories')->ignore($kategoriproduk->id)],
            'slug'         => ['required', 'max:255', Rule::unique('categories')->ignore($kategoriproduk->id)],
            'status'       => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);

        // Cek apakah ada file baru dari FilePond
        if ($request->filled('img_kategori')) {
            $sourcePath = $request->input('img_kategori');

            if (str_starts_with($sourcePath, 'tmp/') && Storage::disk('r2')->exists($sourcePath)) {
                if ($kategoriproduk->img_kategori) {
                    Storage::disk('r2')->delete($kategoriproduk->img_kategori);
                }
                $fileName        = basename($sourcePath);
                $destinationPath = 'kategori-images/' . $fileName;
                Storage::disk('r2')->move($sourcePath, $destinationPath);
                $validatedData['img_kategori'] = $destinationPath;
            }
        } elseif ($request->exists('img_kategori') && $request->input('img_kategori') === null) {
            // Pengguna menghapus gambar
            if ($kategoriproduk->img_kategori && Storage::disk('r2')->exists($kategoriproduk->img_kategori)) {
                Storage::disk('r2')->delete($kategoriproduk->img_kategori);
                $validatedData['img_kategori'] = null;
            }
        } else {
            // Gambar tidak diubah, pertahankan nilai lama
            unset($validatedData['img_kategori']);
        }

        $validatedData['status']    = $request->has('status');
        $validatedData['parent_id'] = $request->filled('parent_id') ? $request->input('parent_id') : null;

        $kategoriproduk->update($validatedData);

        return redirect()
            ->route('kategoriproduk.index', ['type' => $request->input('category_type_edit', 'utama')])
            ->with('success', 'Data Kategori Product Berhasil Diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $kategoriproduk)
    {
        // Cek apakah masih memiliki produk terkait
        if ($kategoriproduk->products()->exists()) {
            $message = 'Kategori tidak dapat dihapus karena masih memiliki produk terkait!';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        // Cek apakah masih memiliki sub kategori
        if ($kategoriproduk->children()->exists()) {
            $message = 'Kategori tidak dapat dihapus karena masih memiliki sub kategori terkait!';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        if ($kategoriproduk->img_kategori) {
            Storage::disk('r2')->delete($kategoriproduk->img_kategori);
        }

        $kategoriproduk->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data Kategori Product Berhasil Dihapus.']);
        }

        return redirect()->route('kategoriproduk.index')->with('success', 'Data Kategori Product Berhasil Dihapus.');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(Category::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }

    /**
     * Menangani unggahan file sementara dari FilePond.
     */
    public function upload(Request $request)
    {
        if ($request->hasFile('img_kategori')) {
            $request->validate([
                'img_kategori' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            ]);

            $file = $request->file('img_kategori');
            $path = $file->store('tmp/kategori-images', 'r2');
            return $path;
        }

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

    /**
     * Mengembalikan daftar kategori utama untuk dropdown parent_id (dipakai via AJAX).
     */
    public function getParentOptions(Request $request)
    {
        $kategoris = Category::whereNull('parent_id')
            ->where('status', true)
            ->get(['id', 'name']);

        return response()->json($kategoris);
    }
}
