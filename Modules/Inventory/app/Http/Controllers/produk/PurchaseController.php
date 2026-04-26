<?php

namespace Modules\Inventory\Http\Controllers\produk;


use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Taxe;
use Barryvdh\DomPDF\Facade\Pdf as Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Purchase;
use Modules\Inventory\Models\Supplier;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Ambil semua status pembayaran yang unik untuk dropdown filter
        $statuses = Purchase::select('status_pembayaran')->distinct()->pluck('status_pembayaran');

        // Mulai query builder
        $query = Purchase::with(['supplier', 'user'])->latest();

        // Terapkan filter pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('referensi', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q_supplier) use ($search) {
                        $q_supplier->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Terapkan filter status jika ada input 'status'
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->input('status'));
        }

        $pembelian = $query->paginate(15)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('inventory::pembelian._pembelian_table', compact('pembelian'))->render();
        }

        // Jika request biasa, kembalikan view lengkap
        return view('inventory::pembelian.index', compact('pembelian', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $statuses = Purchase::select('status_pembayaran')->distinct()->pluck('status_pembayaran');
        return view('inventory::pembelian.create', [
            'title' => 'Tambah Invoice Purchase',
            'supplier' => Supplier::all(),
            'taxes' => Taxe::all(),
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
        $lastPurchase = Purchase::where('referensi', 'like', $prefix . '%')
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
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'referensi' => 'required|string|max:255|unique:purchases',
            'status_barang' => 'required|in:Diterima,Belum Diterima,Dibatalkan',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas', // Lunas Sebagian dihapus dari input manual
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon_tambahan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.taxe_id' => 'nullable|exists:taxes,id',
        ]);

        try {
            // Ambil data pajak yang relevan dalam satu query untuk efisiensi
            $pajakIds = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
            $taxesData = Taxe::whereIn('id', $pajakIds)->get()->keyBy('id');

            $pembelian = DB::transaction(function () use ($validatedData, $request, $taxesData) {
                // 1. Ambil semua produk yang relevan dalam satu query
                $produkIds = collect($validatedData['items'])->pluck('product_id');
                $products = Product::whereIn('id', $produkIds)->get()->keyBy('id');

                // 2. Hitung total dari sisi server untuk keamanan
                $subtotal_keseluruhan = 0;
                $total_pajak_item = 0;
                $itemsForDetail = []; // Array untuk menyimpan data item yang sudah dihitung

                foreach ($validatedData['items'] as $itemData) {
                    $harga_beli = $itemData['harga_beli'];
                    $qty = $itemData['qty'];
                    $diskon_item = $itemData['diskon'] ?? 0;
                    $taxe_id = $itemData['taxe_id'] ?? null;
                    $pajak_rate = $taxe_id ? ($taxesData->get($taxe_id)->rate ?? 0) : 0;

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

                // 3. Buat record Purchase
                $pembelian = Purchase::create([
                    'supplier_id' => $validatedData['supplier_id'],
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

                // 4. Buat record PurchaseItem, update stok, dan update harga beli produk
                foreach ($itemsForDetail as $itemData) {
                    // Buat detail pembelian
                    $pembelian->details()->create([
                        'product_id' => $itemData['product_id'],
                        'qty' => $itemData['qty'],
                        'harga_beli' => $itemData['harga_beli'],
                        'diskon' => $itemData['diskon'] ?? 0,
                        'taxe_id' => $itemData['taxe_id'] ?? null,
                        'subtotal' => $itemData['subtotal'], // Gunakan subtotal yang sudah dihitung (termasuk pajak)
                    ]);
                    // Ambil model produk yang sesuai
                    $produk = $products->get($itemData['product_id']);

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

            return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil disimpan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $pembelian)
    {
        // Eager load relasi untuk efisiensi query dan menghindari N+1 problem
        $pembelian->load('supplier', 'user', 'details.produk');
        $profilToko = Store::first();

        return view('inventory::pembelian.show', [
            'title' => 'Detail Purchase: ' . $pembelian->referensi,
            'pembelian' => $pembelian,
            'profilToko' => $profilToko,

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $pembelian)
    {
        // Eager load relasi untuk efisiensi
        $pembelian->load('details.produk', 'details.pajak');
        $statuses = Purchase::select('status_pembayaran')->distinct()->pluck('status_pembayaran');

        return view('inventory::pembelian.edit', [
            'title' => 'Edit Invoice Purchase: ' . $pembelian->referensi,
            'pembelian' => $pembelian,
            'supplier' => Supplier::all(),
            'taxes' => Taxe::all(),
            'statuses' => $statuses,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $pembelian)
    {
        // --- LOGIKA PEMBATALAN CEPAT DARI HALAMAN INDEX ---
        if ($request->input('status_pembayaran') === 'Dibatalkan' && !$request->has('items')) {
            if ($pembelian->status_pembayaran !== 'Dibatalkan') {
                try {
                    DB::transaction(function () use ($pembelian) {

                        if ($pembelian->status_barang === 'Diterima') {
                            foreach ($pembelian->details as $detail) {
                                Product::where('id', $detail->product_id)->decrement('qty', $detail->qty);
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
                    session()->flash('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
                } catch (\Exception $e) {
                    session()->flash('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
                }
            }
            return redirect()->route('pembelian.index');
        }

        // Membersihkan input mata uang dari format ribuan sebelum validasi
        $request->merge([
            'jumlah_dibayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_dibayar', 0))
        ]);

        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas,Dibatalkan',
            'status_barang' => 'required|in:Diterima,Belum Diterima,Dibatalkan',
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon_tambahan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.taxe_id' => 'nullable|exists:taxes,id',
        ]);

        try {
            $pajakIds = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
            $taxesData = Taxe::whereIn('id', $pajakIds)->get()->keyBy('id');

            DB::transaction(function () use ($validatedData, $pembelian, $taxesData) {
                $statusLama = $pembelian->status_pembayaran;
                $statusBaru = $validatedData['status_pembayaran'];
                $statusBarangLama = $pembelian->status_barang;
                $statusBarangBaru = $validatedData['status_barang'];

                // --- MANAJEMEN STOK ---
                // Ambil semua produk yang relevan untuk data baru dalam satu query
                $newProductIds = collect($validatedData['items'])->pluck('product_id');
                $products = Product::whereIn('id', $newProductIds)->get()->keyBy('id');

                // 1. Kembalikan stok lama jika transaksi sebelumnya aktif (bukan dibatalkan) dan barang sudah diterima
                if ($statusLama !== 'Dibatalkan' && $statusBarangLama === 'Diterima') {
                    foreach ($pembelian->details as $oldDetail) {
                        Product::where('id', $oldDetail->product_id)->decrement('qty', $oldDetail->qty);
                    }
                }

                // 2. Tambah stok baru jika transaksi baru aktif dan barang diterima
                if ($statusBaru !== 'Dibatalkan' && $statusBarangBaru === 'Diterima') {
                    // Validasi stok tidak diperlukan untuk pembelian, hanya penambahan
                    foreach ($validatedData['items'] as $itemData) {
                        Product::where('id', $itemData['product_id'])->increment('qty', $itemData['qty']);
                    }
                }

                if ($statusLama === 'Dibatalkan' && $statusBaru !== 'Dibatalkan') {
                }
                // --- PENGHITUNGAN ULANG TOTAL (SERVER-SIDE) ---
                $subtotal_keseluruhan = 0;
                $total_pajak_item = 0;
                $itemsForDetail = []; // Array untuk menyimpan data item yang sudah dihitung

                foreach ($validatedData['items'] as $itemData) {
                    $taxe_id = $itemData['taxe_id'] ?? null;
                    $pajak_rate = $taxe_id ? ($taxesData->get($taxe_id)->rate ?? 0) : 0;

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
                    'supplier_id' => $validatedData['supplier_id'],
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
                        'product_id' => $itemData['product_id'],
                        'qty' => $itemData['qty'],
                        'harga_beli' => $itemData['harga_beli'],
                        'diskon' => $itemData['diskon'] ?? 0,
                        'taxe_id' => $itemData['taxe_id'] ?? null,
                        'subtotal' => $itemData['subtotal'], // Gunakan subtotal yang sudah dihitung (termasuk pajak)
                    ]);

                    // Update data master produk
                    $produk = $products->get($itemData['product_id']);
                    if ($produk) {
                        $produk->harga_beli = $itemData['harga_beli'];
                        $produk->save();
                    }
                }
            });

            return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $pembelian)
    {
        // try {
        //     DB::transaction(function () use ($pembelian) {
        //         // Kembalikan stok hanya jika barangnya pernah diterima dan status belum 'Dibatalkan'
        //         if ($pembelian->status_barang === 'Diterima' && $pembelian->status_pembayaran !== 'Dibatalkan') {
        //             foreach ($pembelian->details as $detail) {
        //                 Product::where('id', $detail->product_id)->decrement('qty', $detail->qty);
        //             }
        //         }
        //         $pembelian->delete(); // Ini akan menghapus detail juga karena relasi cascade
        //     });
        //     return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil dihapus.');
        // } catch (\Exception $e) {
        //     return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        // }
    }

    public function printThermal(Purchase $pembelian)
    {
        // Eager load relasi yang dibutuhkan
        $pembelian->load('supplier', 'details.produk');
        $profilToko = Store::first();

        return view('inventory::pembelian.thermal', compact('pembelian', 'profilToko'));
    }

    /**
     * Generate PDF for the specified resource.
     */
    public function generatePdf(Purchase $pembelian)
    {
        // Eager load relasi untuk efisiensi
        $pembelian->load('supplier', 'user', 'details.produk', 'details.pajak');
        $profilToko = Store::first();

        // Data yang akan dikirim ke view
        $data = [
            'pembelian' => $pembelian,
            'profilToko' => $profilToko,
        ];

        // Membuat PDF
        $pdf = Pdf::loadView('inventory::pembelian.faktur-pdf', $data);
        return $pdf->stream('faktur-pembelian-' . $pembelian->referensi . '.pdf');
    }
}
