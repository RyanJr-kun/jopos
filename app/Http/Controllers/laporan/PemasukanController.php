<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;

use App\Models\Pemasukan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\KategoriTransaksi;
use Illuminate\Support\Facades\Auth;

class PemasukanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Memuat relasi untuk efisiensi dan memulai query
        $query = Pemasukan::with(['kategori_transaksi', 'user'])->latest();

        $kategoriFilters = KategoriTransaksi::where('jenis', 'pemasukan')
            ->whereHas('pemasukans')
            ->orderBy('nama')
            ->get();
        $allKategoris = KategoriTransaksi::where('jenis', 'pemasukan')
            ->where('status', 1)
            ->orderBy('nama')
            ->get();

        // Terapkan filter pencarian berdasarkan keterangan
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('keterangan', 'like', "%{$search}%")
                ->orWhere('referensi', 'like', "%{$search}%");
        }

        // Terapkan filter berdasarkan ID kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_transaksi_id', $request->input('kategori_id'));
        }

        $pemasukans = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('dashboard.keuangan._pemasukan_table', compact('pemasukans'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('dashboard.keuangan.pemasukan', [
            'title' => 'Pemasukan',
            'pemasukans' => $pemasukans,
            'kategoriFilters' => $kategoriFilters,
            'allKategoris' => $allKategoris,
            'referensi_otomatis' => $this->generateIncomeReferenceNumber()
        ]);
    }

    /**
     * Menghasilkan nomor referensi pemasukan yang unik.
     */
    private function generateIncomeReferenceNumber()
    {
        // Format: IN-YYYYMMDD-XXXX (e.g., IN-20230831-0001)
        $date = now()->format('Ymd');
        $prefix = 'IN-' . $date . '-';

        // Cari referensi terakhir untuk hari ini untuk mendapatkan nomor urut berikutnya
        $lastIncome = Pemasukan::where('referensi', 'like', $prefix . '%')
            ->latest('referensi')
            ->first();

        $sequence = 1;
        if ($lastIncome) {
            $lastSequence = (int) substr($lastIncome->referensi, -4);
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
            'referensi' => 'required|string|max:100|unique:pemasukans',
            'keterangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $validateData['user_id'] = Auth::id();

        Pemasukan::create($validateData);
        Alert::success('Berhasil', 'Pemasukan baru berhasil ditambahkan!');
        return redirect()->route('pemasukan.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pemasukan $pemasukan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pemasukan $pemasukan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function getjson(Pemasukan $pemasukan)
    {
        return response()->json($pemasukan);
    }

    public function update(Request $request, Pemasukan $pemasukan)
    {
        $rules = [
            'kategori_transaksi_id' => 'required|exists:kategori_transaksis,id',
            'tanggal' => 'required|date_format:Y-m-d|before_or_equal:today',
            'jumlah' => 'required|numeric|min:0',
            'referensi' => ['required', 'string', 'max:100', Rule::unique('pemasukans')->ignore($pemasukan->id)],
            'keterangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ];

        $validateData = $request->validate($rules);
        $validateData['user_id'] = Auth::id();

        $pemasukan->update($validateData);
        Alert::success('Berhasil', 'Pemasukan Berhasil Diperbarui!');
        return redirect()->route('pemasukan.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pemasukan $pemasukan)
    {
        $pemasukan->delete();
        Alert::success('Berhasil', 'Pemasukan Berhasil Dihapus!');
        return redirect()->route('pemasukan.index');
    }
}
