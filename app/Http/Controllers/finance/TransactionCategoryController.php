<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use \Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Validation\Rule;

class TransactionCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Hapus 'with('type')' karena 'type' adalah kolom, bukan relasi.
        $query = TransactionCategory::orderBy('id', 'DESC');

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // 2. Filter Type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 3. Filter Status (Aktif/Tidak Aktif atau 1/0)
        if ($request->filled('status')) {
            // Cek apakah filter mengirim teks "Aktif" atau angka "1"
            $status = in_array($request->status, ['Aktif', '1', 1]) ? 1 : 0;
            $query->where('status', $status);
        }

        $kategoris = $query->paginate(15)->withQueryString();
        $types = TransactionCategory::distinct()->pluck('type', 'type')->all();

        if ($request->ajax()) {
            // Kita render fragment yang mencakup TABEL dan PAGINATION
            $html = view('content.finance.kategori', compact('kategoris', 'types'))->fragment('kategori-table-area');

            return response()->json([
                'html' => $html,
                'total' => $kategoris->total() // total keseluruhan data (bukan cuma per halaman)
            ]);
        }

        return view('content.finance.kategori', compact('kategoris', 'types'));
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
            'name' => 'required|max:255|unique:transaction_categories',
            'slug' => 'required|max:255|unique:transaction_categories',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $dataToStore = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status'),
        ];

        TransactionCategory::create($dataToStore);
        return redirect()->route('kategoritransaksi.index')->with('success', 'Kategori Transaksi Baru Berhasil Ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionCategory $kategoritransaksi)
    {
        //
    }

    public function getKategoriJson(TransactionCategory $kategoritransaksi)
    {
        return response()->json($kategoritransaksi);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionCategory $kategoritransaksi)
    {
        return redirect()->route('kategoritransaksi.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionCategory $kategoritransaksi)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255', Rule::unique('transaction_categories')->ignore($kategoritransaksi->id)],
            'slug' => ['required', 'max:255', Rule::unique('transaction_categories')->ignore($kategoritransaksi->id)],
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $dataToUpdate = [
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'status' => $request->has('status')
        ];
        $kategoritransaksi->update($dataToUpdate);
        return redirect()->route('kategoritransaksi.index')->with('success', 'Kategori Transaksi Berhasil Diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionCategory $kategoritransaksi)
    {
        if ($kategoritransaksi->transaksis()->count() > 0) {
            return back()->with('error', 'Kategori Transaksi Tidak Dapat Dihapus Karena Masih Memiliki Transaksi Terkait!');
        }
        $kategoritransaksi->delete();
        return redirect()->route('kategoritransaksi.index')->with('success', 'Kategori Transaksi Berhasil Dihapus!');
    }

    public function chekSlug(Request $request)
    {
        $slug = SlugService::createSlug(TransactionCategory::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
