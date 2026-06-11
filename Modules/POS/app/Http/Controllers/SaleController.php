<?php

namespace Modules\POS\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Customer;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\Taxe;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\SerialNumber;
use Modules\POS\Models\Sale;


class SaleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-penjualan', only: ['index', 'show']),
            new Middleware('permission:create-penjualan', only: ['create', 'store', 'getTodayHistory', 'generateInvoiceNumber']),
            new Middleware('permission:edit-penjualan', only: ['edit', 'update']),
            new Middleware('permission:print-penjualan', only: ['printThermal', 'generatePdf']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $statuses = Sale::select('status_pembayaran')->distinct()->pluck('status_pembayaran');

        $storeId = Auth::user()->employee?->store_id;
        $query = Sale::with(['customer', 'user'])->latest();

        // Filter berdasarkan store_id jika user terikat dengan toko tertentu
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('referensi', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn($qc) => $qc->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->input('status'));
        }

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to   . ' 23:59:59',
            ]);
        }

        $penjualan = $query->paginate(15)->withQueryString();
        if ($request->ajax()) {
            return view('pos::penjualan.partials._penjualan_table', compact('penjualan'))->render();
        }

        return view('pos::penjualan.index', compact('penjualan', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $query = Product::with([
            'category',
            'unit',
            'promotions',
            'stocks',
            'primaryImage',
            'pajak',
        ])
            ->select('products.*')
            ->whereHas('stocks', fn($q) => $q->where('qty', '>', 0));

        if ($request->filled('kategori')) {
            $categoryId  = $request->kategori;
            $categoryIds = Category::where('id', $categoryId)
                ->orWhere('parent_id', $categoryId)
                ->pluck('id');
            $query->whereIn('category_id', $categoryIds);
        }

        $products = $query->orderBy('name_product', 'asc')->get();
        $customers = Customer::query()->where('status', 1)->orderBy('name', 'asc')->get();
        $kategoris = Category::whereNull('parent_id')
            ->with('children')
            ->get();
        $taxes = Taxe::all();
        $referensi = $this->generateInvoiceNumber();
        $banks = Bank::query()->where('is_active', true)->orderBy('nama_bank', 'asc')->get();

        return view('pos::penjualan.create', compact('products', 'customers', 'kategoris', 'taxes', 'referensi', 'banks'));
    }

    private function generateInvoiceNumber()
    {
        $date = now()->format('Ymd');
        $prefix = 'INV-' . $date . '-';

        $lastSale = Sale::where('referensi', 'like', $prefix . '%')
            ->latest('referensi')
            ->first();

        $sequence = 1;
        if ($lastSale) {
            $lastSequence = (int) substr($lastSale->referensi, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Bersihkan input angka yang diformat (mis. "1.500.000" → "1500000")
        $request->merge([
            'jumlah_dibayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_dibayar', 0)),
            'service'        => preg_replace('/[^0-9]/', '', $request->input('service', 0)),
            'ongkir'         => preg_replace('/[^0-9]/', '', $request->input('ongkir', 0)),
            'diskon'         => preg_replace('/[^0-9]/', '', $request->input('diskon', 0)),
        ]);

        $validatedData = $request->validate([
            'customer_id'              => 'nullable|exists:customers,id',
            'tanggal_jatuh_tempo'      => 'nullable|date',
            'referensi'                => 'required|string|unique:sales,referensi',
            'metode_pembayaran'        => 'required|in:TUNAI,TRANSFER,QRIS',
            'bank_id'                  => 'nullable|exists:banks,id|required_if:metode_pembayaran,TRANSFER',
            'catatan'                  => 'nullable|string',
            'jumlah_dibayar'           => 'required|numeric|min:0',
            'service'                  => 'nullable|numeric|min:0',
            'ongkir'                   => 'nullable|numeric|min:0',
            'diskon'                   => 'nullable|numeric|min:0',
            'items'                    => 'required|array|min:1',
            'items.*.product_id'       => 'required|exists:products,id',
            'items.*.jumlah'           => 'required|integer|min:1',
            'items.*.harga_jual'       => 'required|numeric|min:0',
            'items.*.diskon'           => 'required|numeric|min:0',
            'items.*.taxe_id'          => 'nullable|exists:taxes,id',
            'items.*.serial_numbers'   => 'nullable|array',
            'items.*.serial_numbers.*' => 'string',
        ]);

        try {
            // Pre-load data pajak dalam satu query
            $pajakIds  = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
            $taxesData = Taxe::findMany($pajakIds)->keyBy('id');

            $penjualan = DB::transaction(function () use ($validatedData, $taxesData) {

                $produkIds = collect($validatedData['items'])->pluck('product_id');
                $products  = Product::whereIn('id', $produkIds)->get()->keyBy('id');

                // FIX Bug 2: gunakan ?-> agar tidak fatal error jika employee null
                $storeId = Auth::user()->employee?->store_id ?? null;

                // FIX Bug 3: pre-fetch semua stok terkait dalam SATU query (bukan per item)
                $stockQuery = ProductStock::query()
                    ->whereIn('product_id', $produkIds)
                    ->whereNull('product_variant_id');
                if ($storeId) {
                    $stockQuery->where('store_id', $storeId);
                }

                $stockRecords = $stockQuery->get()->keyBy('product_id');

                foreach ($validatedData['items'] as $itemData) {
                    $produk = $products->get($itemData['product_id']);

                    if (!$produk) {
                        throw new \Exception("Produk dengan ID {$itemData['product_id']} tidak ditemukan.");
                    }

                    // FIX Bug 1: Gunakan ProductStock, bukan $produk->qty (tidak ada kolomnya)
                    $stockRecord  = $stockRecords->get($produk->id);
                    $stokTersedia = $stockRecord ? $stockRecord->qty : 0;

                    if ($stokTersedia < (int) $itemData['jumlah']) {
                        throw new \Exception(
                            "Stok untuk produk '{$produk->name_product}' tidak mencukupi. " .
                                "Tersedia: {$stokTersedia}, dibutuhkan: {$itemData['jumlah']}."
                        );
                    }

                    // Validasi serial number wajib
                    if ($produk->wajib_seri) {
                        $snKirim = $itemData['serial_numbers'] ?? [];
                        if (count($snKirim) !== (int) $itemData['jumlah']) {
                            throw new \Exception(
                                "Jumlah nomor seri untuk '{$produk->name_product}' tidak sesuai. " .
                                    "Dibutuhkan: {$itemData['jumlah']}, dikirim: " . count($snKirim) . "."
                            );
                        }

                        $snValid = SerialNumber::where('product_id', $produk->id)
                            ->whereIn('nomor_seri', $snKirim)
                            ->where('status', 'Tersedia')
                            ->count();

                        if ($snValid !== (int) $itemData['jumlah']) {
                            throw new \Exception(
                                "Satu atau lebih nomor seri untuk '{$produk->name_product}' " .
                                    "tidak valid atau sudah terjual."
                            );
                        }
                    }
                }

                // ──────────────────────────────────────────────────────────
                // PASS 2: Hitung total server-side
                // ──────────────────────────────────────────────────────────
                $subtotal_dpp = 0.0;
                $total_pajak  = 0.0;

                foreach ($validatedData['items'] as $itemData) {
                    [$dpp, $pajak] = $this->hitungDppDanPajak(
                        (float) $itemData['harga_jual'],
                        (int)   $itemData['jumlah'],
                        (float) $itemData['diskon'],
                        $itemData['taxe_id'] ?? null,
                        $taxesData
                    );
                    $subtotal_dpp += $dpp;
                    $total_pajak  += $pajak;
                }

                $service       = (float) ($validatedData['service'] ?? 0);
                $ongkir        = (float) ($validatedData['ongkir']  ?? 0);
                $diskon_global = (float) ($validatedData['diskon']  ?? 0);
                $total_akhir   = ($subtotal_dpp + $total_pajak + $service + $ongkir) - $diskon_global;
                $jumlah_dibayar    = (float) $validatedData['jumlah_dibayar'];
                $status_pembayaran = ($jumlah_dibayar >= $total_akhir) ? 'Lunas' : 'Piutang';
                $sisa_piutang      = max(0, $total_akhir - $jumlah_dibayar);
                // ──────────────────────────────────────────────────────────
                // PASS 3: Simpan header penjualan
                // ──────────────────────────────────────────────────────────
                $penjualan = Sale::create([
                    'referensi'         => $validatedData['referensi'],
                    'tanggal_penjualan' => now(),
                    'tanggal_jatuh_tempo' => $validatedData['tanggal_jatuh_tempo'] ?? null,
                    'user_id'           => Auth::id(),
                    'store_id'          => $storeId,
                    'customer_id'       => $validatedData['customer_id'] ?? null,
                    'subtotal'          => $subtotal_dpp,
                    'diskon'            => $diskon_global,
                    'service'           => $service,
                    'ongkir'            => $ongkir,
                    'pajak'             => $total_pajak,
                    'total_akhir'       => $total_akhir,
                    'jumlah_dibayar'    => $jumlah_dibayar,
                    'status_pembayaran' => $status_pembayaran,
                    'metode_pembayaran' => $validatedData['metode_pembayaran'],
                    'bank_id'           => $validatedData['bank_id'] ?? null,
                    'catatan'           => $validatedData['catatan'] ?? null,
                    'sisa_piutang'      => $sisa_piutang,
                ]);

                if ($jumlah_dibayar > 0) {
                    $penjualan->payments()->create([
                        'user_id' => Auth::id(),
                        'tanggal_bayar' => now(),
                        'jumlah_bayar' => $jumlah_dibayar,
                        'metode_pembayaran' => $validatedData['metode_pembayaran'],
                        'bank_id' => $validatedData['bank_id'] ?? null,
                        'referensi_pembayaran' => $validatedData['referensi'],
                        'catatan' => $status_pembayaran === 'Lunas' ? 'Pembayaran Lunas Awal' : 'Pembayaran Uang Muka (DP)'
                    ]);
                }

                // ──────────────────────────────────────────────────────────
                // PASS 4: Simpan item, kurangi stok, update SN
                // FIX Bug 3: gunakan $stockRecords yang sudah di-pre-fetch
                // ──────────────────────────────────────────────────────────
                foreach ($validatedData['items'] as $itemData) {
                    $produk = $products->get($itemData['product_id']);

                    [$dpp, $pajak] = $this->hitungDppDanPajak(
                        (float) $itemData['harga_jual'],
                        (int)   $itemData['jumlah'],
                        (float) $itemData['diskon'],
                        $itemData['taxe_id'] ?? null,
                        $taxesData
                    );

                    $penjualanItem = $penjualan->items()->create([
                        'product_id'  => $produk->id,
                        'jumlah'      => $itemData['jumlah'],
                        'harga_jual'  => $itemData['harga_jual'],
                        'diskon_item' => $itemData['diskon'],
                        'taxe_id'     => $itemData['taxe_id'] ?? null,
                        'pajak_item'  => $pajak,
                        'subtotal'    => $dpp,
                    ]);

                    // FIX Bug 3: pakai stockRecord yang sudah di-fetch, bukan query baru
                    $stockRecord = $stockRecords->get($produk->id);
                    if ($stockRecord) {
                        $stockRecord->decrement('qty', $itemData['jumlah']);
                    }

                    // Update status serial number
                    if ($produk->wajib_seri && !empty($itemData['serial_numbers'])) {
                        SerialNumber::whereIn('nomor_seri', $itemData['serial_numbers'])
                            ->where('product_id', $produk->id)
                            ->where('store_id', $storeId)
                            ->update([
                                'status'       => 'Terjual',
                                'item_sale_id' => $penjualanItem->id,
                            ]);
                    }
                }

                return $penjualan;
            });

            return redirect()
                ->route('penjualan.show', $penjualan->referensi)
                ->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $penjualan)
    {
        $penjualan->load('items.product', 'items.serialNumbers', 'customer', 'user', 'payments.user', 'payments.bank');
        $profilToko = Store::find($penjualan->store_id);
        $banks = Bank::all();

        return view('pos::penjualan.show', [
            'title' => 'Faktur Sale: ' . $penjualan->referensi,
            'penjualan' => $penjualan,
            'profilToko' => $profilToko,
            'banks' => $banks,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $penjualan)
    {
        // Eager load relasi untuk efisiensi
        $penjualan->load('items.product', 'customer', 'user');

        // Ambil data yang dibutuhkan untuk form, mirip seperti method create()
        $customers = Customer::query()->where('status', 1)->orderBy('name', 'asc')->get();
        $taxes = Taxe::all();

        return view('pos::penjualan.edit', [
            'title' => 'Edit Invoice: ' . $penjualan->referensi,
            'penjualan' => $penjualan,
            'customers' => $customers,
            'taxes' => $taxes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $penjualan)
    {
        if ($request->input('status_pembayaran') === 'Batal' && !$request->has('items')) {
            if ($penjualan->status_pembayaran !== 'Batal') {
                try {
                    DB::transaction(function () use ($penjualan) {
                        // PERBAIKAN: Ambil store_id dari transaksi asli
                        $storeId = $penjualan->store_id;

                        foreach ($penjualan->items as $item) {
                            SerialNumber::where('item_sale_id', $item->id)->update([
                                'status'       => 'Tersedia',
                                'item_sale_id' => null,
                            ]);

                            $stockQ = ProductStock::query()->where('product_id', $item->product_id)
                                ->whereNull('product_variant_id');

                            if ($storeId) {
                                $stockQ->where('store_id', $storeId);
                            }

                            $stock = $stockQ->first();
                            if ($stock) {
                                $stock->increment('qty', $item->jumlah);
                            }
                        }

                        $penjualan->update(['status_pembayaran' => 'Batal']);
                    });
                    session()->flash('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan.');
                } catch (\Exception $e) {
                    session()->flash('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
                }
            } else {
                session()->flash('info', 'Transaksi ini sudah dalam status Dibatalkan.');
            }
            return redirect()->route('penjualan.index'); 
        }

        // --- Full update ---
        $request->merge([
            'jumlah_dibayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_dibayar')),
            'service'        => preg_replace('/[^0-9]/', '', $request->input('service', 0)),
            'ongkir'         => preg_replace('/[^0-9]/', '', $request->input('ongkir', 0)),
            'diskon'         => preg_replace('/[^0-9]/', '', $request->input('diskon', 0)),
        ]);

        $validatedData = $request->validate([
            'customer_id'              => 'nullable|exists:customers,id',
            'tanggal_penjualan'        => 'required|date',
            'tanggal_jatuh_tempo'      => 'nullable|date|after:tanggal_penjualan',
            'metode_pembayaran'        => 'required|in:TUNAI,TRANSFER,QRIS',
            'bank_id'                  => 'nullable|exists:banks,id|required_if:metode_pembayaran,TRANSFER',
            'catatan'                  => 'nullable|string',
            'jumlah_dibayar'           => 'required|numeric|min:0',
            'service'                  => 'nullable|numeric|min:0',
            'ongkir'                   => 'nullable|numeric|min:0',
            'diskon'                   => 'nullable|numeric|min:0',
            'items'                    => 'required|array|min:1',
            'items.*.product_id'       => 'required|exists:products,id',
            'items.*.jumlah'           => 'required|integer|min:1',
            'items.*.harga_jual'       => 'required|numeric|min:0',
            'items.*.diskon'           => 'required|numeric|min:0',
            'items.*.taxe_id'          => 'nullable|exists:taxes,id',
            'items.*.serial_numbers'   => 'nullable|array',
            'items.*.serial_numbers.*' => 'string',
        ]);

        try {
            $pajakIds  = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
            $taxesData = Taxe::findMany($pajakIds)->keyBy('id');

            $penjualan = DB::transaction(function () use ($penjualan, $validatedData, $taxesData) {
                $storeId    = $penjualan->store_id;
                $statusLama = $penjualan->status_pembayaran;

                // ── 1. Release Serial Number Lama ────────────────────────────────
                $oldItemIds = $penjualan->items()->pluck('id');
                if ($oldItemIds->isNotEmpty()) {
                    SerialNumber::whereIn('item_sale_id', $oldItemIds)->update([
                        'status'       => 'Tersedia',
                        'item_sale_id' => null, 
                    ]);
                }

                // ── 2. Persiapan Data Produk & Stok ──────────────────────────────
                $oldItemsWithSerials = $penjualan->items; // Cukup ambil items lama
                $newProductIds = collect($validatedData['items'])->pluck('product_id');
                $products      = Product::whereIn('id', $newProductIds)->get()->keyBy('id');

                $allProductIds = $oldItemsWithSerials->pluck('product_id')
                    ->merge($newProductIds)->unique()->values();

                $stockQuery = ProductStock::whereIn('product_id', $allProductIds)
                    ->whereNull('product_variant_id');

                if ($storeId) {
                    $stockQuery->where('store_id', $storeId);
                }
                $stockRecords = $stockQuery->get()->keyBy('product_id');

                // ── 3. Kembalikan Stok Lama (Revert) ─────────────────────────────
                if ($statusLama !== 'Batal') {
                    foreach ($oldItemsWithSerials as $oldItem) {
                        $stock = $stockRecords->get($oldItem->product_id);
                        if ($stock) {
                            $stock->increment('qty', $oldItem->jumlah);
                        }
                    }
                    // Refresh data stok setelah ditambah
                    $stockRecords = $stockQuery->get()->keyBy('product_id'); 
                }

                // ── 4. Validasi Stok Baru & SN Baru ──────────────────────────────
                foreach ($validatedData['items'] as $itemData) {
                    $produk = $products->get($itemData['product_id']);
                    $stock  = $stockRecords->get($itemData['product_id']);
                    $stokTersedia = $stock ? $stock->qty : 0;

                    // Validasi Ketersediaan Stok
                    if (!$produk || $stokTersedia < (int) $itemData['jumlah']) {
                        $nama = $produk?->name_product ?? "ID: {$itemData['product_id']}";
                        throw new \Exception("Stok '{$nama}' tidak mencukupi (tersedia: {$stokTersedia}, dibutuhkan: {$itemData['jumlah']}).");
                    }

                    // Validasi Serial Number
                    if ($produk->wajib_seri) {
                        $snKirim = $itemData['serial_numbers'] ?? [];
                        
                        if (count($snKirim) !== (int) $itemData['jumlah']) {
                            throw new \Exception("Jumlah SN untuk '{$produk->name_product}' tidak sesuai.");
                        }

                        $validSnCount = SerialNumber::where('product_id', $produk->id)
                            ->whereIn('nomor_seri', $snKirim)
                            ->where('status', 'Tersedia') // Karena SN lama sudah di-release di atas, statusnya pasti Tersedia
                            ->count();

                        if ($validSnCount !== (int) $itemData['jumlah']) {
                            throw new \Exception("Satu atau lebih SN untuk '{$produk->name_product}' tidak valid atau sudah terjual.");
                        }
                    }

                    // Kurangi Stok Baru (Deduct)
                    if ($stock) {
                        $stock->decrement('qty', $itemData['jumlah']);
                    }
                }

                // ── 5. Hitung Ulang Total (Server-Side) ──────────────────────────
                $subtotal_dpp = 0.0;
                $total_pajak  = 0.0;

                foreach ($validatedData['items'] as $itemData) {
                    [$dpp, $pajak] = $this->hitungDppDanPajak(
                        (float) $itemData['harga_jual'],
                        (int)   $itemData['jumlah'],
                        (float) $itemData['diskon'],
                        $itemData['taxe_id'] ?? null,
                        $taxesData
                    );
                    $subtotal_dpp += $dpp;
                    $total_pajak  += $pajak;
                }

                $service       = (float) ($validatedData['service'] ?? 0);
                $ongkir        = (float) ($validatedData['ongkir']  ?? 0);
                $diskon_global = (float) ($validatedData['diskon']  ?? 0);
                $total_akhir   = ($subtotal_dpp + $total_pajak + $service + $ongkir) - $diskon_global;
                $jumlah_dibayar = (float) $validatedData['jumlah_dibayar'];
                $status_pembayaran = ($jumlah_dibayar >= $total_akhir) ? 'Lunas' : 'Piutang';
                $sisa_piutang   = max(0, $total_akhir - $jumlah_dibayar);

                // ── 6. Update Header Penjualan ───────────────────────────────────
                $penjualan->update([
                    'customer_id'           => $validatedData['customer_id'] ?? null,
                    'tanggal_penjualan'     => $validatedData['tanggal_penjualan'],
                    'tanggal_jatuh_tempo'   => $validatedData['tanggal_jatuh_tempo'] ?? null,
                    'metode_pembayaran'     => $validatedData['metode_pembayaran'],
                    'bank_id'               => $validatedData['bank_id'] ?? null,
                    'status_pembayaran'     => $status_pembayaran,
                    'subtotal'              => $subtotal_dpp,
                    'diskon'                => $diskon_global,
                    'service'               => $service,
                    'ongkir'                => $ongkir,
                    'pajak'                 => $total_pajak,
                    'total_akhir'           => $total_akhir,
                    'jumlah_dibayar'        => $jumlah_dibayar,
                    'sisa_piutang'          => $sisa_piutang,
                    'catatan'               => $validatedData['catatan'] ?? null,
                ]);

                 if ($jumlah_dibayar > 0) {
                    // Cari data pembayaran pertama berdasarkan ID transaksi ini
                    $pembayaranAwal = $penjualan->payments()->oldest('id')->first();

                    if ($pembayaranAwal) {
                        // Jika sudah ada pembayaran awal, lakukan UPDATE
                        $pembayaranAwal->update([
                            'tanggal_bayar' => $validatedData['tanggal'],
                            'jumlah_bayar' => $jumlah_dibayar,
                            'metode_pembayaran' => $validatedData['metode_pembayaran'],
                            'bank_id' => $validatedData['bank_id'] ?? null,
                            'catatan' => $status_pembayaran === 'Lunas' ? 'Revisi Pembayaran Lunas Awal' : 'Revisi Uang Muka (DP)'
                        ]);
                    } else {
                        // Jika sebelumnya belum ada pembayaran (hutang penuh), lalu saat diedit diisi nominal
                        $penjualan->payments()->create([
                            'user_id' => Auth::id(),
                            'tanggal_bayar' => $validatedData['tanggal'],
                            'jumlah_bayar' => $jumlah_dibayar,
                            'metode_pembayaran' => $validatedData['metode_pembayaran'],
                            'bank_id' => $validatedData['bank_id'] ?? null,
                            'catatan' => $status_pembayaran === 'Lunas' ? 'Pembayaran Lunas Awal' : 'Pembayaran Uang Muka (DP)'
                        ]);
                    }
                }

                // ── 7. Hapus & Buat Ulang Item (Claim SN) ────────────────────────
                $penjualan->items()->delete();

                foreach ($validatedData['items'] as $itemData) {
                    [$dpp, $pajak] = $this->hitungDppDanPajak(
                        (float) $itemData['harga_jual'],
                        (int)   $itemData['jumlah'],
                        (float) $itemData['diskon'],
                        $itemData['taxe_id'] ?? null,
                        $taxesData
                    );

                    $newItem = $penjualan->items()->create([
                        'product_id'  => $itemData['product_id'],
                        'jumlah'      => $itemData['jumlah'],
                        'harga_jual'  => $itemData['harga_jual'],
                        'diskon_item' => $itemData['diskon'],
                        'taxe_id'     => $itemData['taxe_id'] ?? null,
                        'pajak_item'  => $pajak,
                        'subtotal'    => $dpp,
                    ]);

                    if (!empty($itemData['serial_numbers'])) {
                        SerialNumber::whereIn('nomor_seri', $itemData['serial_numbers'])
                            ->where('product_id', $itemData['product_id'])
                            ->update([
                                'status'       => 'Terjual',
                                'item_sale_id' => $newItem->id,
                            ]);
                    }
                }

                return $penjualan->load('items.product', 'customer', 'user');
            });

            return redirect()
                ->route('penjualan.show', $penjualan->referensi)
                ->with('success', 'Transaksi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Generate PDF for the specified resource.
     */
    public function generatePdf(Sale $penjualan)
    {
        // Eager load relasi untuk efisiensi
        $penjualan->load('customer', 'user', 'items.product', 'items.serialNumbers');
        $profilToko = Store::find($penjualan->store_id);

        // Data yang akan dikirim ke view
        $data = [
            'penjualan' => $penjualan,
            'profilToko' => $profilToko,
        ];

        // Membuat PDF
        $pdf = Pdf::loadView('pos::penjualan.faktur-penjualan-pdf', $data);
        return $pdf->stream('faktur-penjualan-' . $penjualan->referensi . '.pdf');
    }

    public function getTodayHistory(Request $request)
    {
        if ($request->ajax()) {

            $storeId = Auth::user()->employee?->store_id;

            $todaySales = Sale::with('customer')
                ->whereDate('created_at', Carbon::today())
                ->when($storeId, function ($query) use ($storeId) {
                    $query->where('store_id', $storeId); // Filter cabang
                })
                ->latest()
                ->get()
                ->map(function ($sale) {
                    return [
                        'referensi' => $sale->referensi,
                        'total_akhir' => $sale->total_akhir,
                        'status' => $sale->status_pembayaran, // Disesuaikan
                        'name' => $sale->customer->name ?? 'Customer Umum',
                        'waktu' => $sale->created_at->format('H:i'),
                    ];
                });

            return response()->json($todaySales);
        }
        // Jika bukan request AJAX, kembalikan ke halaman sebelumnya atau 404
        return redirect()->back();
    }

    /**
     * Menampilkan struk thermal untuk penjualan.
     *
     * @param  Sale  $penjualan
     * @return \Illuminate\View\View
     */
    public function printThermal(Sale $penjualan)
    {
        // Eager load relasi yang dibutuhkan untuk efisiensi
        $penjualan->load('customer', 'user', 'items.product', 'items.serialNumbers');
        $profilToko = Store::find($penjualan->store_id);

        return view('pos::penjualan.thermal', compact('penjualan', 'profilToko'));
    }

    private function hitungDppDanPajak(
        float $harga_jual,
        int   $jumlah,
        float $diskon_item,
        ?int  $taxe_id,
        \Illuminate\Support\Collection $taxesData
    ): array {
        $harga_total          = $harga_jual * $jumlah;
        $harga_setelah_diskon = $harga_total - $diskon_item;

        $pajak_rate = 0.0;
        if ($taxe_id && $taxesData->has($taxe_id)) {
            $pajak_rate = (float) ($taxesData->get($taxe_id)->rate ?? 0);
        }

        // Pajak inklusif: harga sudah termasuk pajak
        $dpp   = $pajak_rate > 0
            ? $harga_setelah_diskon / (1 + ($pajak_rate / 100))
            : $harga_setelah_diskon;

        $pajak = $harga_setelah_diskon - $dpp;

        return [$dpp, $pajak];
    }

    public function storePayment(Request $request, Sale $penjualan)
    {
        // Hilangkan format ribuan (titik) dari nominal input UI sebelum validasi
        if ($request->filled('jumlah_bayar')) {
            $request->merge([
                'jumlah_bayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_bayar'))
            ]);
        }

        $validatedData = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:1|max:' . $penjualan->sisa_piutang,
            'metode_pembayaran' => 'required|in:TUNAI,TRANSFER,QRIS',
            'bank_id' => 'required_if:metode_pembayaran,TRANSFER|nullable|exists:banks,id',
            'referensi_pembayaran' => 'nullable|string|max:100',
            'catatan' => 'nullable|string',
        ], [
            'jumlah_bayar.max' => 'Jumlah pembayaran tidak boleh melebihi sisa hutang (Rp ' . number_format($penjualan->sisa_piutang, 0, ',', '.') . ').',
            'bank_id.required_if' => 'Rekening tujuan wajib dipilih jika menggunakan metode TRANSFER.'
        ]);

        try {
            DB::transaction(function () use ($validatedData, $penjualan) {
                // 1. Masukkan data ke tabel purchase_payments melalui relasi
                $penjualan->payments()->create([
                    'user_id' => Auth::id(),
                    'tanggal_bayar' => $validatedData['tanggal_bayar'],
                    'jumlah_bayar' => $validatedData['jumlah_bayar'],
                    'metode_pembayaran' => $validatedData['metode_pembayaran'],
                    'bank_id' => $validatedData['bank_id'] ?? null,
                    'referensi_pembayaran' => $validatedData['referensi_pembayaran'] ?? null,
                    'catatan' => $validatedData['catatan'] ?? null,
                ]);

                // 2. Kalkulasi akumulasi pembayaran baru
                $totalDibayarBaru = $penjualan->payments()->sum('jumlah_bayar');
                $sisaPiutangBaru  = max(0, $penjualan->total_akhir - $totalDibayarBaru);
                $statusBaru       = $sisaPiutangBaru <= 0 ? 'Lunas' : 'Hutang';

                if ($sisaPiutangBaru < 0) {
                    $sisaPiutangBaru = 0;
                }

                // 3. Tentukan status pembayaran induk
                $statusPembayaranBaru = $sisaPiutangBaru <= 0 ? 'Lunas' : 'Hutang';

                $penjualan->update([
                    'jumlah_dibayar'    => $totalDibayarBaru,
                    'sisa_piutang'      => $sisaPiutangBaru,
                    'status_pembayaran' => $statusBaru,
                ]);
            });

            return redirect()->route('penjualan.show', $penjualan->referensi)
                ->with('success', 'Pembayaran cicilan berhasil dicatat.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
