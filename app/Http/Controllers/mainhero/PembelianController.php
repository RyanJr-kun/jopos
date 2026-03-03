<?php

namespace App\Http\Controllers\mainhero;

use App\Http\Controllers\Controller;

use App\Models\Produk;
use App\Models\Pemasok;
use App\Models\Pembelian;
use App\Models\Pajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ProfilToko;
use Barryvdh\DomPDF\Facade\Pdf;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil semua status pembayaran yang unik untuk dropdown filter
        $statuses = Pembelian::select('status_pembayaran')->distinct()->pluck('status_pembayaran');

        // Mulai query builder
        $query = Pembelian::with(['pemasok', 'user'])->latest();

        // Terapkan filter pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('referensi', 'like', "%{$search}%")
                    ->orWhereHas('pemasok', function ($q_pemasok) use ($search) {
                        $q_pemasok->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        // Terapkan filter status jika ada input 'status'
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->input('status'));
        }

        $pembelian = $query->paginate(10)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('dashboard.pembelian._pembelian_table', compact('pembelian'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('dashboard.pembelian.index', [
            'title' => 'Daftar Invoice Pembelian',
            'pembelian' => $pembelian,
            'statuses' => $statuses,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $statuses = Pembelian::select('status_pembayaran')->distinct()->pluck('status_pembayaran');
        return view('dashboard.pembelian.create', [
            'title' => 'Tambah Invoice Pembelian',
            'pemasok' => Pemasok::all(),
            'pajaks' => Pajak::all(),
            'nomer_referensi' => $this->generatePurchaseInvoiceNumber(),
            'statuses' => $statuses,
        ]);
    }

    /**
     * Menghasilkan nomor referensi pembelian yang unik.
     */
    private function generatePurchaseInvoiceNumber()
    {
        // Format: PO-YYYYMMDD-XXXX (e.g., PO-20230831-0001)
        $date = now()->format('Ymd');
        $prefix = 'PO-' . $date . '-';

        // Cari referensi terakhir untuk hari ini untuk mendapatkan nomor urut berikutnya
        $lastPurchase = Pembelian::where('referensi', 'like', $prefix . '%')
            ->latest('referensi')
            ->first();

        $sequence = 1;
        if ($lastPurchase) {
            $lastSequence = (int) substr($lastPurchase->referensi, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'pemasok_id' => 'required|exists:pemasoks,id',
            'tanggal' => 'required|date',
            'referensi' => 'required|string|max:255|unique:pembelians',
            'status_barang' => 'required|in:Diterima,Belum Diterima,Dibatalkan',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas', // Lunas Sebagian dihapus dari input manual
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon_tambahan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.pajak_id' => 'nullable|exists:pajaks,id',
        ]);

        try {
            // Ambil data pajak yang relevan dalam satu query untuk efisiensi
            $pajakIds = collect($validatedData['items'])->pluck('pajak_id')->filter()->unique();
            $pajaksData = Pajak::whereIn('id', $pajakIds)->get()->keyBy('id');

            $pembelian = DB::transaction(function () use ($validatedData, $request, $pajaksData) {
                // 1. Ambil semua produk yang relevan dalam satu query
                $produkIds = collect($validatedData['items'])->pluck('produk_id');
                $produks = Produk::whereIn('id', $produkIds)->get()->keyBy('id');

                // 2. Hitung total dari sisi server untuk keamanan
                $subtotal_keseluruhan = 0;
                $total_pajak_item = 0;
                $itemsForDetail = []; // Array untuk menyimpan data item yang sudah dihitung

                foreach ($validatedData['items'] as $itemData) {
                    $harga_beli = $itemData['harga_beli'];
                    $qty = $itemData['qty'];
                    $diskon_item = $itemData['diskon'] ?? 0;
                    $pajak_id = $itemData['pajak_id'] ?? null;
                    $pajak_rate = $pajak_id ? ($pajaksData->get($pajak_id)->rate ?? 0) : 0;

                    $subtotal_item = ($harga_beli * $qty) - $diskon_item;
                    $pajak_amount_item = $subtotal_item * ($pajak_rate / 100);
                    $subtotal_item_with_tax = $subtotal_item + $pajak_amount_item;

                    $subtotal_keseluruhan += $subtotal_item_with_tax;
                    $total_pajak_item += $pajak_amount_item;
                    $itemsForDetail[] = array_merge($itemData, ['subtotal' => $subtotal_item_with_tax]);
                }

                $ongkir = $validatedData['ongkir'] ?? 0;
                $diskon_tambahan = $validatedData['diskon_tambahan'] ?? 0;
                $total_akhir = $subtotal_keseluruhan - $diskon_tambahan + $ongkir;
                $jumlah_dibayar = $validatedData['jumlah_dibayar'] ?? 0;

                // 3. Tentukan status pembayaran dan sisa hutang secara otomatis
                $sisa = $total_akhir - $jumlah_dibayar;
                $status_pembayaran = 'Belum Lunas'; // Default status

                // Jika pembayaran pas atau lebih (ada kembalian), statusnya Lunas.
                if ($jumlah_dibayar >= $total_akhir) {
                    $status_pembayaran = 'Lunas';
                } else if ($jumlah_dibayar > 0 && $jumlah_dibayar < $total_akhir) {
                    $status_pembayaran = 'Belum Lunas';
                }

                // 3. Buat record Pembelian
                $pembelian = Pembelian::create([
                    'pemasok_id' => $validatedData['pemasok_id'],
                    'user_id' => Auth::id(),
                    'referensi' => $validatedData['referensi'],
                    'tanggal_pembelian' => $validatedData['tanggal'],
                    'subtotal' => $subtotal_keseluruhan,
                    'diskon' => $diskon_tambahan,
                    'pajak' => $total_pajak_item,
                    'ongkir' => $ongkir,
                    'total_akhir' => $total_akhir,
                    'jumlah_dibayar' => $jumlah_dibayar,
                    'sisa_hutang' => $sisa, // Simpan sisa, bisa positif (hutang) atau negatif (kembalian)
                    'status_pembayaran' => $status_pembayaran, // Gunakan status yang sudah ditentukan
                    'status_barang' => $validatedData['status_barang'],
                    'catatan' => $validatedData['catatan'],
                ]);

                // 4. Buat record PembelianDetail, update stok, dan update harga beli produk
                foreach ($itemsForDetail as $itemData) {
                    // Buat detail pembelian
                    $pembelian->details()->create([
                        'produk_id' => $itemData['produk_id'],
                        'qty' => $itemData['qty'],
                        'harga_beli' => $itemData['harga_beli'],
                        'diskon' => $itemData['diskon'] ?? 0,
                        'pajak_id' => $itemData['pajak_id'] ?? null,
                        'subtotal' => $itemData['subtotal'], // Gunakan subtotal yang sudah dihitung (termasuk pajak)
                    ]);
                    // Ambil model produk yang sesuai
                    $produk = $produks->get($itemData['produk_id']);

                    // Update data di tabel produk master
                    $produk->harga_beli = $itemData['harga_beli'];

                    // Tambah stok hanya jika status barang 'Diterima'
                    if ($validatedData['status_barang'] === 'Diterima') {
                        $produk->qty += $itemData['qty'];
                    }
                    $produk->save(); // Simpan perubahan (harga beli, harga jual, dan/atau stok)
                }

                return $pembelian;
            });

            Alert::success('Berhasil', 'Transaksi pembelian berhasil disimpan.');
            return redirect()->route('pembelian.index');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembelian $pembelian)
    {
        // Eager load relasi untuk efisiensi query dan menghindari N+1 problem
        $pembelian->load('pemasok', 'user', 'details.produk');
        $profilToko = ProfilToko::first();

        return view('dashboard.pembelian.show', [
            'title' => 'Detail Pembelian: ' . $pembelian->referensi,
            'pembelian' => $pembelian,
            'profilToko' => $profilToko,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembelian $pembelian)
    {
        // Eager load relasi untuk efisiensi
        $pembelian->load('details.produk', 'details.pajak');
        $statuses = Pembelian::select('status_pembayaran')->distinct()->pluck('status_pembayaran');

        return view('dashboard.pembelian.edit', [
            'title' => 'Edit Invoice Pembelian: ' . $pembelian->referensi,
            'pembelian' => $pembelian,
            'pemasok' => Pemasok::all(),
            'pajaks' => Pajak::all(),
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        // --- LOGIKA PEMBATALAN CEPAT DARI HALAMAN INDEX ---
        if ($request->input('status_pembayaran') === 'Dibatalkan' && !$request->has('items')) {
            if ($pembelian->status_pembayaran !== 'Dibatalkan') {
                try {
                    DB::transaction(function () use ($pembelian) {

                        if ($pembelian->status_barang === 'Diterima') {
                            foreach ($pembelian->details as $detail) {
                                Produk::where('id', $detail->produk_id)->decrement('qty', $detail->qty);
                            }
                        }
                        // Update status dan reset pembayaran
                        $pembelian->update([
                            'status_pembayaran' => 'Dibatalkan',
                            'status_barang' => 'Dibatalkan',
                            'jumlah_dibayar' => 0,
                            'sisa_hutang' => 0
                        ]);
                    });
                    Alert::success('Berhasil', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
                } catch (\Exception $e) {
                    Alert::error('Gagal', 'Gagal membatalkan transaksi: ' . $e->getMessage());
                }
            }
            return redirect()->route('pembelian.index');
        }

        // Membersihkan input mata uang dari format ribuan sebelum validasi
        $request->merge([
            'jumlah_dibayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_dibayar', 0))
        ]);

        $validatedData = $request->validate([
            'pemasok_id' => 'required|exists:pemasoks,id',
            'tanggal' => 'required|date',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas,Dibatalkan',
            'status_barang' => 'required|in:Diterima,Belum Diterima,Dibatalkan',
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon_tambahan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.pajak_id' => 'nullable|exists:pajaks,id',
        ]);

        try {
            $pajakIds = collect($validatedData['items'])->pluck('pajak_id')->filter()->unique();
            $pajaksData = Pajak::whereIn('id', $pajakIds)->get()->keyBy('id');

            DB::transaction(function () use ($validatedData, $pembelian, $pajaksData) {
                $statusLama = $pembelian->status_pembayaran;
                $statusBaru = $validatedData['status_pembayaran'];
                $statusBarangLama = $pembelian->status_barang;
                $statusBarangBaru = $validatedData['status_barang'];

                // --- MANAJEMEN STOK ---
                // Ambil semua produk yang relevan untuk data baru dalam satu query
                $newProdukIds = collect($validatedData['items'])->pluck('produk_id');
                $produks = Produk::whereIn('id', $newProdukIds)->get()->keyBy('id');

                // 1. Kembalikan stok lama jika transaksi sebelumnya aktif (bukan dibatalkan) dan barang sudah diterima
                if ($statusLama !== 'Dibatalkan' && $statusBarangLama === 'Diterima') {
                    foreach ($pembelian->details as $oldDetail) {
                        Produk::where('id', $oldDetail->produk_id)->decrement('qty', $oldDetail->qty);
                    }
                }

                // 2. Tambah stok baru jika transaksi baru aktif dan barang diterima
                if ($statusBaru !== 'Dibatalkan' && $statusBarangBaru === 'Diterima') {
                    // Validasi stok tidak diperlukan untuk pembelian, hanya penambahan
                    foreach ($validatedData['items'] as $itemData) {
                        Produk::where('id', $itemData['produk_id'])->increment('qty', $itemData['qty']);
                    }
                }

                if ($statusLama === 'Dibatalkan' && $statusBaru !== 'Dibatalkan') {
                }
                // --- PENGHITUNGAN ULANG TOTAL (SERVER-SIDE) ---
                $subtotal_keseluruhan = 0;
                $total_pajak_item = 0;
                $itemsForDetail = []; // Array untuk menyimpan data item yang sudah dihitung

                foreach ($validatedData['items'] as $itemData) {
                    $pajak_id = $itemData['pajak_id'] ?? null;
                    $pajak_rate = $pajak_id ? ($pajaksData->get($pajak_id)->rate ?? 0) : 0;

                    $subtotal_item = ($itemData['harga_beli'] * $itemData['qty']) - ($itemData['diskon'] ?? 0);
                    $pajak_amount_item = $subtotal_item * ($pajak_rate / 100);
                    $subtotal_item_with_tax = $subtotal_item + $pajak_amount_item;

                    $subtotal_keseluruhan += $subtotal_item_with_tax;
                    $total_pajak_item += $pajak_amount_item;
                    $itemsForDetail[] = array_merge($itemData, ['subtotal' => $subtotal_item_with_tax]);
                }

                $ongkir = $validatedData['ongkir'] ?? 0;
                $diskon_tambahan = $validatedData['diskon_tambahan'] ?? 0;
                $total_akhir = $subtotal_keseluruhan - $diskon_tambahan + $ongkir;
                $jumlah_dibayar = $validatedData['jumlah_dibayar'] ?? 0;

                // Tentukan status pembayaran dan sisa hutang secara otomatis (konsisten dengan method store)
                $sisa = $total_akhir - $jumlah_dibayar;
                $status_pembayaran_server = 'Belum Lunas'; // Default status
                if ($jumlah_dibayar >= $total_akhir) {
                    $status_pembayaran_server = 'Lunas';
                } else if ($jumlah_dibayar > 0 && $jumlah_dibayar < $total_akhir) {
                    $status_pembayaran_server = 'Belum Lunas';
                }

                // --- UPDATE DATA PEMBELIAN ---
                $pembelian->update([
                    'pemasok_id' => $validatedData['pemasok_id'],
                    'tanggal_pembelian' => $validatedData['tanggal'],
                    'user_id' => Auth::id(), // Tambahkan update user_id untuk melacak siapa yang mengedit
                    'subtotal' => $subtotal_keseluruhan,
                    'diskon' => $diskon_tambahan,
                    'pajak' => $total_pajak_item,
                    'ongkir' => $ongkir,
                    'total_akhir' => $total_akhir,
                    'jumlah_dibayar' => $jumlah_dibayar,
                    'sisa_hutang' => $sisa,
                    'status_pembayaran' => $statusBaru === 'Dibatalkan' ? 'Dibatalkan' : $status_pembayaran_server,
                    'status_barang' => $validatedData['status_barang'], // Status barang tetap dari input
                    'catatan' => $validatedData['catatan'],
                ]);

                // Hapus detail lama dan buat yang baru
                $pembelian->details()->delete();

                foreach ($itemsForDetail as $itemData) {
                    $pembelian->details()->create([
                        'produk_id' => $itemData['produk_id'],
                        'qty' => $itemData['qty'],
                        'harga_beli' => $itemData['harga_beli'],
                        'diskon' => $itemData['diskon'] ?? 0,
                        'pajak_id' => $itemData['pajak_id'] ?? null,
                        'subtotal' => $itemData['subtotal'], // Gunakan subtotal yang sudah dihitung (termasuk pajak)
                    ]);

                    // Update data master produk
                    $produk = $produks->get($itemData['produk_id']);
                    if ($produk) {
                        $produk->harga_beli = $itemData['harga_beli'];
                        $produk->save();
                    }
                }
            });

            Alert::success('Berhasil', 'Transaksi pembelian berhasil diperbarui.');
            return redirect()->route('pembelian.index');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian)
    {
        // try {
        //     DB::transaction(function () use ($pembelian) {
        //         // Kembalikan stok hanya jika barangnya pernah diterima dan status belum 'Dibatalkan'
        //         if ($pembelian->status_barang === 'Diterima' && $pembelian->status_pembayaran !== 'Dibatalkan') {
        //             foreach ($pembelian->details as $detail) {
        //                 Produk::where('id', $detail->produk_id)->decrement('qty', $detail->qty);
        //             }
        //         }
        //         $pembelian->delete(); // Ini akan menghapus detail juga karena relasi cascade
        //     });
        //     Alert::success('Berhasil', 'Transaksi pembelian berhasil dihapus.');
        //     return redirect()->route('pembelian.index');
        // } catch (\Exception $e) {
        //     Alert::error('Gagal', 'Gagal menghapus transaksi: ' . $e->getMessage());
        //     return back();
        // }
    }

    public function printThermal(Pembelian $pembelian)
    {
        // Eager load relasi yang dibutuhkan
        $pembelian->load('pemasok', 'details.produk');
        $profilToko = ProfilToko::first();

        return view('dashboard.pembelian.thermal', compact('pembelian', 'profilToko'));
    }

    /**
     * Generate PDF for the specified resource.
     */
    public function generatePdf(Pembelian $pembelian)
    {
        // Eager load relasi untuk efisiensi
        $pembelian->load('pemasok', 'user', 'details.produk', 'details.pajak');
        $profilToko = ProfilToko::first();

        // Data yang akan dikirim ke view
        $data = [
            'pembelian' => $pembelian,
            'profilToko' => $profilToko,
        ];

        // Membuat PDF
        $pdf = Pdf::loadView('dashboard.pembelian.faktur-pdf', $data);
        return $pdf->stream('faktur-pembelian-' . $pembelian->referensi . '.pdf');
    }
}
