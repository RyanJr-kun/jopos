<?php

namespace Modules\Inventory\Http\Controllers\produk;


use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\Taxe;
use Barryvdh\DomPDF\Facade\Pdf as Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\ProductVariant;
use Modules\Inventory\Models\Purchase;
use Modules\Inventory\Models\Supplier;

class PurchaseController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-pembelian', only: ['index', 'show', 'generatePurchaseInvoiceNumber']),
            new Middleware('permission:create-pembelian', only: ['create', 'store']),
            new Middleware('permission:edit-pembelian', only: ['edit', 'update']),
            new Middleware('permission:delete-pembelian', only: ['destroy']),
            new Middleware('permission:print-pembelian', only: ['printThermal', 'generatePdf']),
        ];
    }
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
            return view('inventory::pembelian.partials._pembelian_table', compact('pembelian'))->render();
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
        $supplier = Supplier::query()->where('status', 1)->get();
        $taxes = Taxe::all();
        $nomer_referensi = $this->generatePurchaseInvoiceNumber();
        $barangs = Purchase::getStatusBarangs();
        $payments = Purchase::getPaymentStatus();
        $options = Purchase::getPaymentMethods();
        $banks = Bank::all();

        return view('inventory::pembelian.create', compact('supplier', 'taxes', 'nomer_referensi', 'statuses', 'barangs', 'payments', 'options', 'banks'));
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
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date|after:tanggal',
            'referensi' => 'required|string|max:255|unique:purchases',
            'status_barang' => 'required|in:Diterima,Pre Order,Retur,Batal',
            'status_pembayaran' => 'required|in:Lunas,Hutang,Batal',
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon_tambahan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.taxe_id' => 'nullable|exists:taxes,id',
            'metode_pembayaran' => 'required|in:TUNAI,TRANSFER,QRIS',
            'bank_id' => 'required_if:metode_pembayaran,TRANSFER|nullable|exists:banks,id',
        ]);

        try {
            $pajakIds = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
            $taxesData = Taxe::findMany($pajakIds)->keyBy('id');

            $pembelian = DB::transaction(function () use ($validatedData, $request, $taxesData) {
                $storeId = Auth::user()->employee?->store_id ?? null;

                $produkIds = collect($validatedData['items'])->pluck('product_id')->unique();
                $products = Product::whereIn('id', $produkIds)->get()->keyBy('id');

                $variantIds = collect($validatedData['items'])->pluck('product_variant_id')->filter()->unique();
                $variants = ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

                $subtotal_keseluruhan = 0;
                $total_pajak_item = 0;
                $itemsForDetail = [];

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

                // 3. Tentukan status pembayaran
                $sisa = $total_akhir - $jumlah_dibayar;
                $status_pembayaran = 'Hutang';

                if ($jumlah_dibayar >= $total_akhir) {
                    $status_pembayaran = 'Lunas';
                }

                // 4. Buat record Purchase
                $pembelian = Purchase::create([
                    'store_id' => $storeId, // Wajib diisi berdasarkan struktur tabel purchases
                    'supplier_id' => $validatedData['supplier_id'],
                    'user_id' => Auth::id(),
                    'referensi' => $validatedData['referensi'],
                    'tanggal_pembelian' => $validatedData['tanggal'],
                    'tanggal_jatuh_tempo' => $validatedData['tanggal_jatuh_tempo'] ?? null,
                    'subtotal' => $subtotal_keseluruhan,
                    'diskon' => $diskon_tambahan,
                    'pajak' => $total_pajak_item,
                    'ongkir' => $ongkir,
                    'total_akhir' => $total_akhir,
                    'jumlah_dibayar' => $jumlah_dibayar,
                    'sisa_hutang' => $sisa,
                    'status_pembayaran' => $status_pembayaran,
                    'status_barang' => $validatedData['status_barang'],
                    'metode_pembayaran' => $validatedData['metode_pembayaran'],
                    'bank_id' => $validatedData['bank_id'] ?? null,
                    'catatan' => $validatedData['catatan'],
                ]);

                if ($jumlah_dibayar > 0) {
                    $pembelian->payments()->create([
                        'user_id' => Auth::id(),
                        'tanggal_bayar' => $validatedData['tanggal'],
                        'jumlah_bayar' => $jumlah_dibayar,
                        'metode_pembayaran' => $validatedData['metode_pembayaran'],
                        'bank_id' => $validatedData['bank_id'] ?? null,
                        'referensi_pembayaran' => $validatedData['referensi'],
                        'catatan' => $status_pembayaran === 'Lunas' ? 'Pembayaran Lunas Awal' : 'Pembayaran Uang Muka (DP)'
                    ]);
                }

                // 5. Buat record PurchaseItem, update stok, dan update harga beli
                foreach ($itemsForDetail as $itemData) {
                    $variantId = $itemData['product_variant_id'] ?? null;

                    // A. Buat detail pembelian
                    $pembelian->details()->create([
                        'product_id' => $itemData['product_id'],
                        'product_variant_id' => $variantId, // Simpan ID Varian jika ada
                        'qty' => $itemData['qty'],
                        'harga_beli' => $itemData['harga_beli'],
                        'diskon' => $itemData['diskon'] ?? 0,
                        'taxe_id' => $itemData['taxe_id'] ?? null,
                        'subtotal' => $itemData['subtotal'],
                    ]);

                    // B. Update data harga beli produk master atau varian
                    if ($variantId && $variants->has($variantId)) {
                        // Jika varian, update harga beli di tabel product_variants
                        $varian = $variants->get($variantId);
                        $varian->harga_beli = $itemData['harga_beli'];
                        $varian->save();
                    } else {
                        // Jika produk simple, update harga beli di tabel products
                        $produk = $products->get($itemData['product_id']);
                        if ($produk) {
                            $produk->harga_beli = $itemData['harga_beli'];
                            $produk->save();
                        }
                    }

                    // C. Tambah stok menggunakan tabel `product_stocks`
                    if ($validatedData['status_barang'] === 'Diterima') {
                        // Cari baris stok yang sudah ada, atau buat baru jika belum ada di toko ini
                        $stockRecord = ProductStock::firstOrCreate(
                            [
                                'store_id' => $storeId,
                                'product_id' => $itemData['product_id'],
                                'product_variant_id' => $variantId,
                            ],
                            ['qty' => 0] // Nilai default jika harus create baru
                        );

                        $stockRecord->increment('qty', $itemData['qty']);
                    }
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
        $pembelian->load(['supplier', 'user', 'details.produk', 'payments.user', 'payments.bank']);
        $profilToko = Store::query()->first();
        $banks = Bank::all(); // Diperlukan untuk pilihan bank di dalam modal cicilan

        return view('inventory::pembelian.show', [
            'title' => 'Detail Purchase: ' . $pembelian->referensi,
            'pembelian' => $pembelian,
            'profilToko' => $profilToko,
            'banks' => $banks, // Kirim data bank ke view
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $pembelian)
    {
        // Eager load relasi untuk efisiensi, termasuk varian dan gambarnya
        $pembelian->load([
            'details.produk.primaryImage',
            'details.pajak',
            'details.varian.options'
        ]);

        $supplier = Supplier::query()->where('status', 1)->get();
        $taxes = Taxe::all();
        $nomer_referensi = $this->generatePurchaseInvoiceNumber();
        $barangs = Purchase::getStatusBarangs();
        $payments = Purchase::getPaymentStatus();
        $options = Purchase::getPaymentMethods();
        $banks = Bank::all();

        $statuses = Purchase::select('status_pembayaran')->distinct()->pluck('status_pembayaran');

        return view('inventory::pembelian.edit', [
            'title' => 'Edit Invoice Purchase: ' . $pembelian->referensi,
            'pembelian' => $pembelian,
            'pemasok' => Supplier::where('status', 1)->get(),
            'taxes' => Taxe::all(),
            'statuses' => $statuses,
            'supplier' => $supplier,
            'taxes' => $taxes,
            'nomer_referensi' => $nomer_referensi,
            'barangs' => $barangs,
            'payments' => $payments,
            'options' => $options,
            'banks' => $banks,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $pembelian)
    {
        // Karena tabel purchases butuh store_id (seperti di fungsi store)
        $storeId = $pembelian->store_id;

        // --- LOGIKA PEMBATALAN CEPAT DARI HALAMAN INDEX ---
        if ($request->input('status_pembayaran') === 'Batal' && !$request->has('items')) {
            if ($pembelian->status_pembayaran !== 'Batal') {
                try {
                    DB::transaction(function () use ($pembelian, $storeId) {
                        // Jika barangnya pernah diterima, kurangi stoknya dari product_stocks
                        if ($pembelian->status_barang === 'Diterima') {
                            foreach ($pembelian->details as $detail) {
                                $stockRecord = ProductStock::query()
                                    ->where('store_id', $storeId)
                                    ->where('product_id', $detail->product_id)
                                    ->where('product_variant_id', $detail->product_variant_id)
                                    ->first();

                                if ($stockRecord && $stockRecord->qty >= $detail->qty) {
                                    $stockRecord->decrement('qty', $detail->qty);
                                }
                            }
                        }
                        // Update status dan reset pembayaran
                        $pembelian->update([
                            'status_pembayaran' => 'Batal',
                            'status_barang' => 'Batal',
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
            'bank_id' => 'nullable|exists:banks,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date|after:tanggal',
            'status_pembayaran' => 'required|in:Lunas,Hutang,Batal',
            'status_barang' => 'required|in:Diterima,Belum Diterima,Batal',
            'jumlah_dibayar' => 'nullable|numeric|min:0',
            'ongkir' => 'nullable|numeric|min:0',
            'diskon_tambahan' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga_beli' => 'required|numeric|min:0',
            'items.*.diskon' => 'nullable|numeric|min:0',
            'items.*.taxe_id' => 'nullable|exists:taxes,id',
        ]);

        try {
            $pajakIds = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
            $taxesData = Taxe::findMany($pajakIds)->keyBy('id');

            DB::transaction(function () use ($validatedData, $pembelian, $taxesData, $storeId) {
                $statusLama = $pembelian->status_pembayaran;
                $statusBaru = $validatedData['status_pembayaran'];
                $statusBarangLama = $pembelian->status_barang;
                $statusBarangBaru = $validatedData['status_barang'];

                // --- MANAJEMEN STOK ---
                $newProductIds = collect($validatedData['items'])->pluck('product_id')->unique();
                $products = \Modules\Inventory\Models\Product::whereIn('id', $newProductIds)->get()->keyBy('id');

                $variantIds = collect($validatedData['items'])->pluck('product_variant_id')->filter()->unique();
                $variants = \Modules\Inventory\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

                // 1. Kembalikan stok lama (Reset) dari product_stocks
                if ($statusLama !== 'Batal' && $statusBarangLama === 'Diterima') {
                    foreach ($pembelian->details as $oldDetail) {
                        $stockRecord = ProductStock::query()
                            ->where('store_id', $storeId)
                            ->where('product_id', $oldDetail->product_id)
                            ->where('product_variant_id', $oldDetail->product_variant_id)
                            ->first();

                        if ($stockRecord && $stockRecord->qty >= $oldDetail->qty) {
                            $stockRecord->decrement('qty', $oldDetail->qty);
                        }
                    }
                }

                // 2. Tambah stok baru jika transaksi baru aktif dan barang diterima
                if ($statusBaru !== 'Batal' && $statusBarangBaru === 'Diterima') {
                    foreach ($validatedData['items'] as $itemData) {
                        $variantId = $itemData['product_variant_id'] ?? null;

                        $stockRecord = \App\Models\ProductStock::firstOrCreate(
                            [
                                'store_id' => $storeId,
                                'product_id' => $itemData['product_id'],
                                'product_variant_id' => $variantId,
                            ],
                            ['qty' => 0]
                        );

                        $stockRecord->increment('qty', $itemData['qty']);
                    }
                }

                // --- PENGHITUNGAN ULANG TOTAL (SERVER-SIDE) ---
                $subtotal_keseluruhan = 0;
                $total_pajak_item = 0;
                $itemsForDetail = [];

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

                // Tentukan status pembayaran
                $sisa = $total_akhir - $jumlah_dibayar;
                $status_pembayaran_server = 'Hutang';
                if ($jumlah_dibayar >= $total_akhir) {
                    $status_pembayaran_server = 'Lunas';
                }

                // --- UPDATE DATA PEMBELIAN ---
                $pembelian->update([
                    'supplier_id' => $validatedData['supplier_id'],
                    'tanggal_pembelian' => $validatedData['tanggal'],
                    'tanggal_jatuh_tempo' => $validatedData['tanggal_jatuh_tempo'] ?? null,
                    'user_id' => Auth::id(),
                    'subtotal' => $subtotal_keseluruhan,
                    'diskon' => $diskon_tambahan,
                    'pajak' => $total_pajak_item,
                    'ongkir' => $ongkir,
                    'total_akhir' => $total_akhir,
                    'jumlah_dibayar' => $jumlah_dibayar,
                    'sisa_hutang' => $sisa,
                    'status_pembayaran' => $statusBaru === 'Batal' ? 'Batal' : $status_pembayaran_server,
                    'status_barang' => $validatedData['status_barang'],
                    'catatan' => $validatedData['catatan'],
                ]);

                // Hapus detail lama dan buat yang baru
                $pembelian->details()->delete();

                foreach ($itemsForDetail as $itemData) {
                    $variantId = $itemData['product_variant_id'] ?? null;

                    $pembelian->details()->create([
                        'product_id' => $itemData['product_id'],
                        'product_variant_id' => $variantId,
                        'qty' => $itemData['qty'],
                        'harga_beli' => $itemData['harga_beli'],
                        'diskon' => $itemData['diskon'] ?? 0,
                        'taxe_id' => $itemData['taxe_id'] ?? null,
                        'subtotal' => $itemData['subtotal'],
                    ]);

                    // Update harga beli master (Produk Induk atau Varian)
                    if ($variantId && $variants->has($variantId)) {
                        $varian = $variants->get($variantId);
                        $varian->harga_beli = $itemData['harga_beli'];
                        $varian->save();
                    } else {
                        $produk = $products->get($itemData['product_id']);
                        if ($produk) {
                            $produk->harga_beli = $itemData['harga_beli'];
                            $produk->save();
                        }
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
        //         // Kembalikan stok hanya jika barangnya pernah diterima dan status belum 'Batal'
        //         if ($pembelian->status_barang === 'Diterima' && $pembelian->status_pembayaran !== 'Batal') {
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
        $profilToko = Store::query()->first();

        return view('inventory::pembelian.thermal', compact('pembelian', 'profilToko'));
    }

    /**
     * Generate PDF for the specified resource.
     */
    public function generatePdf(Purchase $pembelian)
    {
        // Eager load relasi untuk efisiensi
        $pembelian->load('supplier', 'user', 'details.produk', 'details.pajak');
        $profilToko = Store::query()->first();

        // Data yang akan dikirim ke view
        $data = [
            'pembelian' => $pembelian,
            'profilToko' => $profilToko,
        ];

        // Membuat PDF
        $pdf = Pdf::loadView('inventory::pembelian.faktur-pdf', $data);
        return $pdf->stream('faktur-pembelian-' . $pembelian->referensi . '.pdf');
    }

    public function storePayment(Request $request, Purchase $pembelian)
    {
        // Hilangkan format ribuan (titik) dari nominal input UI sebelum validasi
        if ($request->filled('jumlah_bayar')) {
            $request->merge([
                'jumlah_bayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_bayar'))
            ]);
        }

        $validatedData = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:1|max:' . $pembelian->sisa_hutang,
            'metode_pembayaran' => 'required|in:TUNAI,TRANSFER,QRIS',
            'bank_id' => 'required_if:metode_pembayaran,TRANSFER|nullable|exists:banks,id',
            'referensi_pembayaran' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ], [
            'jumlah_bayar.max' => 'Jumlah pembayaran tidak boleh melebihi sisa hutang (Rp ' . number_format($pembelian->sisa_hutang, 0, ',', '.') . ').',
            'bank_id.required_if' => 'Rekening tujuan wajib dipilih jika menggunakan metode TRANSFER.'
        ]);

        try {
            DB::transaction(function () use ($validatedData, $pembelian) {
                // 1. Masukkan data ke tabel purchase_payments melalui relasi
                $pembelian->payments()->create([
                    'user_id' => Auth::id(),
                    'tanggal_bayar' => $validatedData['tanggal_bayar'],
                    'jumlah_bayar' => $validatedData['jumlah_bayar'],
                    'metode_pembayaran' => $validatedData['metode_pembayaran'],
                    'bank_id' => $validatedData['bank_id'] ?? null,
                    'referensi_pembayaran' => $validatedData['referensi_pembayaran'] ?? null,
                    'catatan' => $validatedData['catatan'] ?? null,
                ]);

                // 2. Kalkulasi akumulasi pembayaran baru
                $totalDibayarBaru = $pembelian->jumlah_dibayar + $validatedData['jumlah_bayar'];
                $sisaHutangBaru = $pembelian->total_akhir - $totalDibayarBaru;

                if ($sisaHutangBaru < 0) {
                    $sisaHutangBaru = 0;
                }

                // 3. Tentukan status pembayaran induk
                $statusPembayaranBaru = $sisaHutangBaru <= 0 ? 'Lunas' : 'Hutang';

                // 4. Update baris data purchases master
                $pembelian->update([
                    'jumlah_dibayar' => $totalDibayarBaru,
                    'sisa_hutang' => $sisaHutangBaru,
                    'status_pembayaran' => $statusPembayaranBaru
                ]);
            });

            return redirect()->route('pembelian.show', $pembelian->referensi)
                ->with('success', 'Pembayaran cicilan berhasil dicatat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
