<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use \Cviebrock\EloquentSluggable\Services\SlugService;


class KategoriProdukController extends Controller
{
    public function index(Request $request)
    {
        $statuses = KategoriProduk::select('status')->distinct()->pluck('status');
        $query = KategoriProduk::withCount('produks')->latest();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nama', 'LIKE', "%{$search}%");
        }
        if ($request->filled('status')) {
            $statusValue = $request->input('status') === 'Aktif' ? 1 : 0;
            $query->where('status', $statusValue);
        }

        $kategoris = $query->paginate(15)->withQueryString();
        if ($request->ajax()) {
            return view('dashboard.produk._kategori_produk_table', compact('kategoris'))->render();
        }

        return view('dashboard.produk.kategoriproduk', [
            'title' => 'Data Kategori Produk',
            'kategoris' => $kategoris,
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'img_kategori' => 'nullable|string|starts_with:tmp/',
            'nama' => 'required|max:255|unique:kategori_produks',
            'slug' => 'required|max:255|unique:kategori_produks',
            'status' => 'nullable|boolean',
        ]);

        // Jika ada file yang diunggah melalui FilePond
        if ($request->filled('img_kategori')) {
            $sourcePath = $request->input('img_kategori'); // Path dari folder tmp
            $fileName = basename($sourcePath);
            $destinationPath = 'kategori-images/' . $fileName;

            // Pindahkan file dari tmp ke direktori tujuan
            if (Storage::disk('public')->exists($sourcePath)) {
                Storage::disk('public')->move($sourcePath, $destinationPath);
                $validatedData['img_kategori'] = $destinationPath; // Simpan path baru
            } else {
                // Hapus path jika file tidak ditemukan untuk mencegah error
                unset($validatedData['img_kategori']);
            }
        }

        $validatedData['status'] = $request->has('status');
        $kategori = KategoriProduk::create($validatedData);

        // Cek jika request adalah AJAX
        if ($request->wantsJson()) {
            // Muat relasi dan format tanggal untuk konsistensi dengan data yang ada
            $kategori->loadCount('produks');
            $kategori->created_at_formatted = $kategori->created_at->translatedFormat('d M Y');

            return response()->json([
                'success' => true,
                'message' => 'Kategori baru berhasil ditambahkan.',
                'data'    => $kategori
            ], 201);
        }

        // Respons standar jika bukan AJAX
        Alert::success('Berhasil', 'Kategori Baru Berhasil Ditambahkan.');
        return redirect()->route('kategoriproduk.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriProduk $kategori_produk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriProduk $kategoriproduk)
    {
        //
    }

    public function getKategoriJson(KategoriProduk $kategoriproduk)
    {
        return response()->json($kategoriproduk);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriProduk $kategoriproduk)
    {
        $rules = [
            'img_kategori' => 'nullable|string',
            'nama' => ['required', 'max:255', Rule::unique('kategori_produks')->ignore($kategoriproduk->id)],
            'slug' => ['required', 'max:255', Rule::unique('kategori_produks')->ignore($kategoriproduk->id)],
            'status' => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);

        // Cek apakah ada file baru yang diunggah
        if ($request->filled('img_kategori')) {
            $sourcePath = $request->input('img_kategori');

            // Pastikan ini adalah file baru dari tmp, bukan path file lama
            if (strpos($sourcePath, 'tmp/') === 0 && Storage::disk('public')->exists($sourcePath)) {
                // Hapus gambar lama jika ada
                if ($kategoriproduk->img_kategori) {
                    Storage::disk('public')->delete($kategoriproduk->img_kategori);
                }

                // Pindahkan gambar baru dari tmp ke lokasi permanen
                $fileName = basename($sourcePath);
                $destinationPath = 'kategori-images/' . $fileName;
                Storage::disk('public')->move($sourcePath, $destinationPath);
                $validatedData['img_kategori'] = $destinationPath;
            }
            // Menangani kasus jika pengguna menghapus gambar yang ada melalui FilePond
        } elseif ($request->exists('img_kategori') && $request->input('img_kategori') === null) {
            if ($kategoriproduk->img_kategori && Storage::disk('public')->exists($kategoriproduk->img_kategori)) {
                Storage::disk('public')->delete($kategoriproduk->img_kategori);
                $validatedData['img_kategori'] = null;
            }
        }


        $validatedData['status'] = $request->has('status');
        $kategoriproduk->update($validatedData);

        if ($request->wantsJson()) {
            $kategoriproduk->loadCount('produks');
            $kategoriproduk->created_at_formatted = $kategoriproduk->created_at->translatedFormat('d M Y');

            return response()->json([
                'success' => true,
                'message' => 'Data Kategori Produk Berhasil Diperbarui.',
                'data'    => $kategoriproduk
            ]);
        }

        Alert::success('Berhasil', 'Data Kategori Produk Berhasil Diperbarui.');
        return redirect()->route('kategoriproduk.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriProduk $kategoriproduk)
    {
        if ($kategoriproduk->produks()->exists()) {
            $message = 'Kategori Produk tidak dapat dihapus karena masih memiliki produk terkait!';
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            Alert::error('Gagal', $message);
            return back();
        }

        if ($kategoriproduk->img_kategori) {
            Storage::disk('public')->delete($kategoriproduk->img_kategori);
        }

        $kategoriproduk->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Data Kategori Produk Berhasil Dihapus.']);
        }

        Alert::success('Berhasil', 'Data Kategori Produk Berhasil Dihapus.');
        return redirect()->route('kategoriproduk.index');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(KategoriProduk::class, 'slug', $request->nama);
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
            // Simpan file ke direktori 'tmp' di dalam 'storage/app/public'
            $path = $file->store('tmp/kategori-images', 'public');

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
