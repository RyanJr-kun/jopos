<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\KategoriTransaksi;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Mulai query dengan eager loading untuk efisiensi
        $query = Pengeluaran::with(['user', 'kategori_transaksi'])->latest();

        $kategoriFilters = KategoriTransaksi::where('jenis', 'pengeluaran')
            ->whereHas('pengeluarans')
            ->orderBy('nama')
            ->get();
        $allKategoris = KategoriTransaksi::where('jenis', 'pengeluaran')
            ->where('status', 1)
            ->orderBy('nama')
            ->get();

        // Terapkan filter pencarian jika ada
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('keterangan', 'like', "%{$search}%")
                ->orWhere('referensi', 'like', "%{$search}%");
        }

        // Terapkan filter kategori jika ada
        if ($request->filled('kategori_id')) {
            $query->where('kategori_transaksi_id', $request->input('kategori_id'));
        }

        $pengeluarans = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('dashboard.keuangan._pengeluaran_table', compact('pengeluarans'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('dashboard.keuangan.pengeluaran', [
            'title' => 'Data Pengeluaran',
            'pengeluarans' => $pengeluarans,
            'kategoriFilters' => $kategoriFilters,
            'allKategoris' => $allKategoris,
            'referensi_otomatis' => $this->generateExpenseReferenceNumber()
        ]);
    }

    /**
     * Menghasilkan nomor referensi pengeluaran yang unik.
     */
    private function generateExpenseReferenceNumber()
    {
        // Format: EX-YYYYMMDD-XXXX (e.g., EX-20231027-0001)
        $date = now()->format('Ymd');
        $prefix = 'EX-' . $date . '-';

        // Cari referensi terakhir untuk hari ini untuk mendapatkan nomor urut berikutnya
        $lastExpense = \App\Models\Pengeluaran::where('referensi', 'like', $prefix . '%')
            ->latest('referensi')
            ->first();

        $sequence = 1;
        if ($lastExpense) {
            $lastSequence = (int) substr($lastExpense->referensi, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
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
        $validateData = $request->validate([
            'kategori_transaksi_id' => 'required|exists:kategori_transaksis,id',
            'tanggal' => 'required|date_format:Y-m-d|before_or_equal:today',
            'jumlah' => 'required|numeric|min:0',
            'referensi' => 'required|string|max:100|unique:pengeluarans',
            'keterangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $validateData['user_id'] = Auth::id();

        Pengeluaran::create($validateData);
        Alert::success('Berhasil', 'Pengeluaran baru berhasil ditambahkan!');
        return redirect()->route('pengeluaran.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pengeluaran $pengeluaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pengeluaran $pengeluaran)
    {
        //
    }

    public function getjson(Pengeluaran $pengeluaran)
    {
        return response()->json($pengeluaran);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $rules = [
            'kategori_transaksi_id' => 'required|exists:kategori_transaksis,id',
            'tanggal' => 'required|date_format:Y-m-d|before_or_equal:today',
            'jumlah' => 'required|numeric|min:0',
            'referensi' => ['required', 'string', 'max:100', Rule::unique('pengeluarans')->ignore($pengeluaran->id)],
            'keterangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ];

        $validateData = $request->validate($rules);
        $validateData['user_id'] = Auth::id();

        $pengeluaran->update($validateData);
        Alert::success('Berhasil', 'Pengeluaran Berhasil Diperbarui!');
        return redirect()->route('pengeluaran.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();
        Alert::success('Berhasil', 'Pengeluaran Berhasil Dihapus!');
        return redirect()->route('pengeluaran.index');
    }
}
