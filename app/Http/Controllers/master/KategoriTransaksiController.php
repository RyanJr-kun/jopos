<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\KategoriTransaksi;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Validation\Rule;

class KategoriTransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mulai query builder
        $query = KategoriTransaksi::latest();

        // Terapkan filter pencarian berdasarkan nama
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Terapkan filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Terapkan filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $kategoris = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('dashboard.keuangan._kategori_table', compact('kategoris'))->render();
        }

        return view('dashboard.keuangan.kategori', [
            'title' => 'Kategori Transaksi',
            'kategoris' => $kategoris,
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
        $validated = $request->validate([
            'nama' => 'required|max:255|unique:kategori_transaksis',
            'slug' => 'required|max:255|unique:kategori_transaksis',
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $dataToStore = [
            'nama' => $validated['nama'],
            'slug' => $validated['slug'],
            'jenis' => $validated['jenis'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $request->has('status'),
        ];

        KategoriTransaksi::create($dataToStore);
        Alert::success('Berhasil', 'Kategori Transaksi Baru Berhasil Ditambahkan!');
        return redirect()->route('kategoritransaksi.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(KategoriTransaksi $kategoritransaksi)
    {
        //
    }

    public function getKategoriJson(KategoriTransaksi $kategoritransaksi)
    {
        return response()->json($kategoritransaksi);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KategoriTransaksi $kategoritransaksi)
    {
        return redirect()->route('kategoritransaksi.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KategoriTransaksi $kategoritransaksi)
    {
        $validated = $request->validate([
            'nama' => ['required', 'max:255', Rule::unique('kategori_transaksis')->ignore($kategoritransaksi->id)],
            'slug' => ['required', 'max:255', Rule::unique('kategori_transaksis')->ignore($kategoritransaksi->id)],
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $dataToUpdate = [
            'nama' => $validated['nama'],
            'slug' => $validated['slug'],
            'jenis' => $validated['jenis'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'status' => $request->has('status')
        ];
        $kategoritransaksi->update($dataToUpdate);
        Alert::success('Berhasil', 'Kategori Transaksi Berhasil Diperbarui!');
        return redirect()->route('kategoritransaksi.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KategoriTransaksi $kategoritransaksi)
    {
        if ($kategoritransaksi->transaksis()->count() > 0) {
            Alert::error('Gagal', 'Kategori Transaksi Tidak Dapat Dihapus Karena Masih Memiliki Transaksi Terkait!');
            return back();
        }
        $kategoritransaksi->delete();
        Alert::success('Berhasil', 'Kategori Transaksi Berhasil Dihapus!');
        return redirect()->route('kategoritransaksi.index');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(KategoriTransaksi::class, 'slug', $request->nama);
        return response()->json(['slug' => $slug]);
    }
}
