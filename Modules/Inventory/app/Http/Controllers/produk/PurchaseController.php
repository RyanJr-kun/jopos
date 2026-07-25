<?php

namespace Modules\Inventory\Http\Controllers\produk;

use App\Http\Controllers\Controller;
use App\Models\Account;
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
    $statuses = Purchase::getPaymentStatus();
    $barangs = Purchase::getStatusBarangs();

    $user = Auth::user();
    $canViewAllStores = $user->can('view-toko-gudang');
    $employeeStoreId = $user->employee?->store_id;

    $query = Purchase::with(['supplier', 'user', 'store'])->latest();

    // --- Scoping store: inti dari fiturnya ---
    if ($canViewAllStores) {
      // admin/manager: filter opsional lewat dropdown
      if ($request->filled('store_id')) {
        $query->where('store_id', $request->input('store_id'));
      }
    } else {
      $query->where('store_id', $employeeStoreId);
    }

    if ($request->filled('search')) {
      $search = $request->input('search');
      $query->where(function ($q) use ($search) {
        $q->where('referensi', 'like', "%{$search}%")->orWhereHas('supplier', fn($q_s) => $q_s->where('name', 'like', "%{$search}%"));
      });
    }

    if ($request->filled('payment')) {
      $query->where('status_pembayaran', $request->input('payment'));
    }

    if ($request->filled('barang')) {
      $query->where('status_barang', $request->input('barang'));
    }

    if ($request->filled('date_from') && $request->filled('date_to')) {
      $query->whereDate('tanggal_pembelian', '>=', $request->input('date_from'))->whereDate('tanggal_pembelian', '<=', $request->input('date_to'));
    }

    $pembelian = $query->paginate(15)->withQueryString();

    if ($request->ajax()) {
      return view('inventory::pembelian.partials._pembelian_table', compact('pembelian', 'canViewAllStores'))->render();
    }

    $stores = $canViewAllStores ? Store::orderBy('name_toko')->get() : collect();

    return view('inventory::pembelian.index', compact('pembelian', 'statuses', 'barangs', 'stores', 'canViewAllStores'));
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
    $accounts = Account::query()->where('tipe_akun', 'bank')->get();

    return view('inventory::pembelian.create', compact('supplier', 'taxes', 'nomer_referensi', 'statuses', 'barangs', 'payments', 'options', 'accounts'));
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
    $request->merge([
      'jumlah_dibayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_dibayar', 0)),
      'ongkir' => preg_replace('/[^0-9]/', '', $request->input('ongkir', 0)),
      'diskon' => preg_replace('/[^0-9]/', '', $request->input('diskon', 0)),
    ]);

    $validatedData = $request->validate([
      'supplier_id' => 'required|exists:suppliers,id',
      'tanggal' => 'required|date',
      'tanggal_jatuh_tempo' => 'nullable|date',
      'referensi' => 'required|string|max:255|unique:purchases',
      'status_barang' => 'required|in:Diterima,Pre Order,Retur,Batal',
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
      'account_id' => 'required_if:metode_pembayaran,TRANSFER|nullable|exists:accounts,id',
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
          $harga_beli = (float) $itemData['harga_beli'];
          $qty = (int) $itemData['qty'];
          $diskon_item = (float) ($itemData['diskon'] ?? 0);
          $taxe_id = $itemData['taxe_id'] ?? null;
          $pajak_rate = $taxe_id ? $taxesData->get($taxe_id)->rate ?? 0 : 0;

          // DPP (Dasar Pengenaan Pajak) = Harga murni setelah diskon item
          $dpp_item = $harga_beli * $qty - $diskon_item;
          $pajak_amount_item = $dpp_item * ($pajak_rate / 100);

          // [PERBAIKAN 1]: Pisahkan subtotal murni dan pajak
          $subtotal_keseluruhan += $dpp_item;
          $total_pajak_item += $pajak_amount_item;

          // [PERBAIKAN 1]: subtotal_with_tax hanya dipakai untuk detail item, bukan untuk grand total
          $subtotal_item_with_tax = $dpp_item + $pajak_amount_item;
          $itemsForDetail[] = array_merge($itemData, ['subtotal' => $subtotal_item_with_tax]);
        }

        $ongkir = (float) ($validatedData['ongkir'] ?? 0);
        $diskon_tambahan = (float) ($validatedData['diskon_tambahan'] ?? 0);

        // Perhitungan grand total sekarang aman
        $total_akhir = $subtotal_keseluruhan + $total_pajak_item - $diskon_tambahan + $ongkir;
        $jumlah_dibayar = (float) ($validatedData['jumlah_dibayar'] ?? 0);

        // [PERBAIKAN 3]: Cegah hutang minus jika bayar lebih
        $sisa = max(0, $total_akhir - $jumlah_dibayar);
        $status_pembayaran = $jumlah_dibayar >= $total_akhir ? 'Lunas' : 'Hutang';

        // 4. Buat record Purchase
        $pembelian = Purchase::create([
          'store_id' => $storeId,
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
          'account_id' => $validatedData['account_id'] ?? null,
          'catatan' => $validatedData['catatan'],
        ]);

        if ($jumlah_dibayar > 0) {
          $pembelian->payments()->create([
            'user_id' => Auth::id(),
            'tanggal_bayar' => $validatedData['tanggal'],
            'jumlah_bayar' => $jumlah_dibayar,
            'metode_pembayaran' => $validatedData['metode_pembayaran'],
            'account_id' => $validatedData['account_id'] ?? null,
            'referensi_pembayaran' => $validatedData['referensi'],
            'catatan' => $status_pembayaran === 'Lunas' ? 'Pembayaran Lunas Awal' : 'Pembayaran Uang Muka (DP)',
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

          $subtotal_dpp_global = (int) $subtotal_keseluruhan > 0 ? (int) $subtotal_keseluruhan : 1;

          $ongkir = (int) ($validatedData['ongkir'] ?? 0);
          $diskon_tambahan = (int) ($validatedData['diskon_tambahan'] ?? 0);
          $net_adjustment_global = $ongkir - $diskon_tambahan;

          if ($validatedData['status_barang'] === 'Diterima') {
            $qty_baru = (int) $itemData['qty'];
            $harga_beli_form = (int) $itemData['harga_beli'];
            $diskon_item = (int) ($itemData['diskon'] ?? 0);

            // ─── 1. KALKULASI HARGA BELI RIIL PER UNIT (TOTAL COST OF ACQUISITION) ───

            // A. Cari Nilai Dasar Item (DPP)
            $dpp_item = $harga_beli_form * $qty_baru - $diskon_item;

            // B. Cari Pajak Item Ini
            $taxe_id = $itemData['taxe_id'] ?? null;
            $pajak_rate = $taxe_id ? $taxesData->get($taxe_id)->rate ?? 0 : 0;
            $pajak_item = (int) round($dpp_item * ($pajak_rate / 100));

            // C. Distribusikan Ongkir & Diskon Global ke Item Ini
            $proporsi_item = $dpp_item / $subtotal_dpp_global;
            $beban_global_item = $net_adjustment_global * $proporsi_item;

            // D. Total Modal untuk Baris Item Ini
            $total_modal_item = $dpp_item + $pajak_item + $beban_global_item;

            // E. HPP Riil (Modal Akhir Per Pcs) - Dibulatkan ke integer mutlak
            $harga_beli_riil = $qty_baru > 0 ? (int) round($total_modal_item / $qty_baru) : $harga_beli_form;

            // ─── 2. TERAPKAN MOVING AVERAGE DENGAN HARGA RIIL ───

            $queryStokLama = ProductStock::where('product_id', $itemData['product_id']);
            if ($variantId) {
              $queryStokLama->where('product_variant_id', $variantId);
            } else {
              $queryStokLama->whereNull('product_variant_id');
            }

            $total_stok_lama = (int) $queryStokLama->sum('qty');
            $total_stok_baru = $total_stok_lama + $qty_baru;

            if ($variantId && $variants->has($variantId)) {
              $varian = $variants->get($variantId);
              $old_hpp = (int) $varian->harga_beli;

              $nilai_aset_lama = $total_stok_lama * $old_hpp;
              $nilai_aset_baru = $qty_baru * $harga_beli_riil;

              // Rumus Average Costing
              $hpp_rata_rata = $total_stok_baru > 0 ? (int) round(($nilai_aset_lama + $nilai_aset_baru) / $total_stok_baru) : $harga_beli_riil;

              $varian->harga_beli = $hpp_rata_rata;
              $varian->save();
            } else {
              $produk = $products->get($itemData['product_id']);
              if ($produk) {
                $old_hpp = (int) $produk->harga_beli;

                $nilai_aset_lama = $total_stok_lama * $old_hpp;
                $nilai_aset_baru = $qty_baru * $harga_beli_riil;

                // Rumus Average Costing
                $hpp_rata_rata = $total_stok_baru > 0 ? (int) round(($nilai_aset_lama + $nilai_aset_baru) / $total_stok_baru) : $harga_beli_riil;

                $produk->harga_beli = $hpp_rata_rata;
                $produk->save();
              }
            }

            // ─── 3. INJEKSI STOK KE GUDANG AKTIF ───

            $stockRecord = ProductStock::firstOrCreate(
              [
                'store_id' => $storeId,
                'product_id' => $itemData['product_id'],
                'product_variant_id' => $variantId,
              ],
              ['qty' => 0],
            );

            $stockRecord->increment('qty', $qty_baru);
          }
        }

        return $pembelian;
      });

      return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil disimpan.');
    } catch (\Exception $e) {
      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Display the specified resource.
   */
  public function show(Purchase $pembelian)
  {
    $pembelian->load(['supplier', 'user', 'details.produk', 'payments.user', 'payments.bank']);
    $profilToko = $pembelian->store;
    $accounts = Account::query()->where('tipe_akun', 'bank')->get(); // Diperlukan untuk pilihan bank di dalam modal cicilan

    return view('inventory::pembelian.show', compact('pembelian', 'profilToko', 'accounts'));
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Purchase $pembelian)
  {
    // Eager load relasi untuk efisiensi, termasuk varian dan gambarnya
    $pembelian->load(['details.produk.primaryImage', 'details.pajak', 'details.varian.options']);

    $supplier = Supplier::query()->where('status', 1)->get();
    $taxes = Taxe::all();
    $nomer_referensi = $this->generatePurchaseInvoiceNumber();
    $barangs = Purchase::getStatusBarangs();
    $payments = Purchase::getPaymentStatus();
    $options = Purchase::getPaymentMethods();
    $accounts = Account::query()->where('tipe_akun', 'bank')->get();
    $pemasok = Supplier::query()->where('status', 1)->get();

    $statuses = Purchase::select('status_pembayaran')->distinct()->pluck('status_pembayaran');

    return view('inventory::pembelian.edit', compact('accounts', 'pembelian', 'statuses', 'supplier', 'taxes', 'nomer_referensi', 'barangs', 'payments', 'options', 'pemasok'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Purchase $pembelian)
  {
    $storeId = $pembelian->store_id;

    // --- LOGIKA PEMBATALAN CEPAT DARI HALAMAN INDEX ---
    if ($request->input('status_pembayaran') === 'Batal' && !$request->has('items')) {
      if ($pembelian->status_pembayaran !== 'Batal') {
        if ($pembelian->status_barang === 'Diterima') {
          foreach ($pembelian->details as $detail) {
            $stockRecord = ProductStock::query()->where('store_id', $storeId)->where('product_id', $detail->product_id)->where('product_variant_id', $detail->product_variant_id)->first();

            $currentQty = $stockRecord ? $stockRecord->qty : 0;
            if ($currentQty < $detail->qty) {
              $namaProduk = $detail->produk->name_product ?? 'Produk';
              return redirect()
                ->route('pembelian.index')
                ->with('error', "Gagal dibatalkan! Sebagian '{$namaProduk}' sudah terjual. Sisa stok saat ini ({$currentQty}) tidak cukup untuk ditarik ({$detail->qty}).");
            }
          }
        }

        try {
          DB::transaction(function () use ($pembelian, $storeId) {
            if ($pembelian->status_barang === 'Diterima') {
              foreach ($pembelian->details as $detail) {
                $stockRecord = ProductStock::query()->where('store_id', $storeId)->where('product_id', $detail->product_id)->where('product_variant_id', $detail->product_variant_id)->first();

                if ($stockRecord) {
                  $stockRecord->decrement('qty', $detail->qty);
                }
              }
            }

            $pembelian->update([
              'status_pembayaran' => 'Batal',
              'status_barang' => 'Batal',
              'sisa_hutang' => 0,
            ]);
          });
          return redirect()->route('pembelian.index')->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
        } catch (\Exception $e) {
          return redirect()
            ->route('pembelian.index')
            ->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
      }
      return redirect()->route('pembelian.index');
    }

    // --- LOGIKA UPDATE FULL (DARI HALAMAN EDIT) ---
    if ($pembelian->status_pembayaran === 'Batal') {
      return back()->with('error', 'Transaksi yang sudah dibatalkan tidak dapat diedit kembali.');
    }

    $request->merge([
      'jumlah_dibayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_dibayar', 0)),
    ]);

    $validatedData = $request->validate([
      'supplier_id' => 'required|exists:suppliers,id',
      'tanggal' => 'required|date',
      'tanggal_jatuh_tempo' => 'nullable|date|after:tanggal',
      'status_pembayaran' => 'required|in:Lunas,Hutang,Batal',
      'status_barang' => 'required|in:Diterima,Pre Order,Retur,Batal',
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
      'account_id' => 'required_if:metode_pembayaran,TRANSFER|nullable|exists:accounts,id',
    ]);

    try {
      $pajakIds = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();
      $taxesData = Taxe::findMany($pajakIds)->keyBy('id');

      DB::transaction(function () use ($validatedData, $pembelian, $taxesData, $storeId) {
        $statusLama = $pembelian->status_pembayaran;
        $statusBaru = $validatedData['status_pembayaran'];
        $statusBarangLama = $pembelian->status_barang;
        $statusBarangBaru = $validatedData['status_barang'];

        $newProductIds = collect($validatedData['items'])->pluck('product_id')->unique();
        $products = \Modules\Inventory\Models\Product::whereIn('id', $newProductIds)->get()->keyBy('id');

        $variantIds = collect($validatedData['items'])->pluck('product_variant_id')->filter()->unique();
        $variants = \Modules\Inventory\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');

        // 1. REVERT STOK DAN REVERT HPP LAMA
        $oldItemsWithPrices = clone $pembelian->details;

        // ID dari item BARU (form yang baru disubmit)
        $newProductIds = collect($validatedData['items'])->pluck('product_id')->unique();
        $newVariantIds = collect($validatedData['items'])->pluck('product_variant_id')->filter()->unique();
        $newTaxeIds = collect($validatedData['items'])->pluck('taxe_id')->filter()->unique();

        // [FIX 4] ID dari item LAMA (sebelum diedit) — supaya produk yang dihapus dari form

        // tetap bisa dikoreksi HPP-nya saat proses revert
        $oldProductIds = $oldItemsWithPrices->pluck('product_id')->unique();
        $oldVariantIds = $oldItemsWithPrices->pluck('product_variant_id')->filter()->unique();
        $oldTaxeIds = $oldItemsWithPrices->pluck('taxe_id')->filter()->unique();

        // Gabungan lama + baru — dipakai baik di blok revert maupun blok forward
        $allProductIds = $newProductIds->merge($oldProductIds)->unique();
        $allVariantIds = $newVariantIds->merge($oldVariantIds)->unique();
        $allTaxeIds = $newTaxeIds->merge($oldTaxeIds)->unique();

        $products = \Modules\Inventory\Models\Product::whereIn('id', $allProductIds)->get()->keyBy('id');
        $variants = \Modules\Inventory\Models\ProductVariant::whereIn('id', $allVariantIds)->get()->keyBy('id');
        $taxesData = Taxe::findMany($allTaxeIds)->keyBy('id');

        // 1. REVERT STOK DAN REVERT HPP LAMA
        if ($statusLama !== 'Batal' && $statusBarangLama === 'Diterima') {
          // Pajak dari item lama + baru, supaya tarif pajak item lama selalu tersedia

          $old_subtotal_global = $pembelian->subtotal > 0 ? (int) $pembelian->subtotal : 1;
          $old_net_adjustment = (int) $pembelian->ongkir - (int) $pembelian->diskon;

          foreach ($oldItemsWithPrices as $oldDetail) {
            // A. Hitung HPP Riil (modal akhir) versi nota lama
            $old_dpp_item = (int) $oldDetail->harga_beli * (int) $oldDetail->qty - (int) ($oldDetail->diskon ?? 0);
            $old_proporsi = $old_dpp_item / $old_subtotal_global;

            $old_tax_rate = $oldDetail->taxe_id ? $taxesData->get($oldDetail->taxe_id)->rate ?? 0 : 0;
            $old_pajak = (int) round($old_dpp_item * ($old_tax_rate / 100));

            $old_beban_global = $old_net_adjustment * $old_proporsi;
            $old_total_modal = $old_dpp_item + $old_pajak + $old_beban_global;

            $old_hpp_riil = $oldDetail->qty > 0 ? (int) round($old_total_modal / $oldDetail->qty) : (int) $oldDetail->harga_beli;

            // B. Stok GLOBAL (semua toko) — basis rata-rata, konsisten dgn perhitungan maju
            $queryStokGlobal = ProductStock::where('product_id', $oldDetail->product_id);
            if ($oldDetail->product_variant_id) {
              $queryStokGlobal->where('product_variant_id', $oldDetail->product_variant_id);
            } else {
              $queryStokGlobal->whereNull('product_variant_id');
            }
            $stok_global_sekarang = (int) $queryStokGlobal->sum('qty');
            $sisa_stok_setelah_revert = $stok_global_sekarang - (int) $oldDetail->qty;

            // C. Stok fisik toko pusat — yang benar-benar akan dikurangi
            $stockRecord = ProductStock::query()->where('store_id', $storeId)->where('product_id', $oldDetail->product_id)->where('product_variant_id', $oldDetail->product_variant_id)->first();

            $stok_pusat_sekarang = $stockRecord ? (int) $stockRecord->qty : 0;

            // D. Validasi stok cukup sebelum ditarik
            if ($stok_pusat_sekarang < (int) $oldDetail->qty) {
              $namaProduk = $oldDetail->produk->name_product ?? 'Produk';
              throw new \Exception(
                "Gagal diedit! Sebagian stok '{$namaProduk}' dari pembelian ini sudah ditransfer ke toko cabang / terjual. Stok toko pusat saat ini ({$stok_pusat_sekarang}) tidak cukup untuk ditarik ({$oldDetail->qty}).",
              );
            }

            // E. REVERT HPP DI MASTER (Reverse Average Formula)
            // [FIX 5] Cari target langsung dari koleksi gabungan, tanpa cek ->has()
            // (koleksi $products/$variants sudah pasti memuat entri dari item lama)
            $targetModel = $oldDetail->product_variant_id ? $variants->get($oldDetail->product_variant_id) : $products->get($oldDetail->product_id);

            if ($targetModel) {
              $hpp_sekarang = (int) $targetModel->harga_beli;

              if ($sisa_stok_setelah_revert > 0) {
                $aset_sekarang = $stok_global_sekarang * $hpp_sekarang;
                $aset_yg_dibatalkan = (int) $oldDetail->qty * $old_hpp_riil;

                $hpp_mundur = (int) round(($aset_sekarang - $aset_yg_dibatalkan) / $sisa_stok_setelah_revert);
                $targetModel->harga_beli = $hpp_mundur;
              }
              // Jika sisa stok global 0 atau minus, biarkan harga_beli seperti terakhir kali

              $targetModel->save();
            }

            // F. REVERT STOK FISIK (toko pusat)
            if ($stockRecord) {
              $stockRecord->decrement('qty', $oldDetail->qty);
            }
          }
        }

        // 2. HITUNG GRAND TOTAL & SIAPKAN DISTRIBUSI BEBAN
        $subtotal_keseluruhan = 0; // Murni DPP tanpa pajak
        $total_pajak_item = 0;
        $itemsForDetail = [];

        foreach ($validatedData['items'] as $itemData) {
          $taxe_id = $itemData['taxe_id'] ?? null;
          $pajak_rate = $taxe_id ? $taxesData->get($taxe_id)->rate ?? 0 : 0;

          $dpp_item = (int) $itemData['harga_beli'] * (int) $itemData['qty'] - (int) ($itemData['diskon'] ?? 0);
          $pajak_amount_item = (int) round($dpp_item * ($pajak_rate / 100));

          $subtotal_keseluruhan += $dpp_item;
          $total_pajak_item += $pajak_amount_item;

          $subtotal_item_with_tax = $dpp_item + $pajak_amount_item;
          $itemsForDetail[] = array_merge($itemData, ['subtotal' => $subtotal_item_with_tax]);
        }

        $ongkir = (int) ($validatedData['ongkir'] ?? 0);
        $diskon_tambahan = (int) ($validatedData['diskon_tambahan'] ?? 0);
        $net_adjustment_global = $ongkir - $diskon_tambahan;
        $subtotal_dpp_global = $subtotal_keseluruhan > 0 ? $subtotal_keseluruhan : 1;

        $total_akhir = $subtotal_keseluruhan + $total_pajak_item - $diskon_tambahan + $ongkir;
        $jumlah_dibayar = (int) ($validatedData['jumlah_dibayar'] ?? 0);

        $sisa = max(0, $total_akhir - $jumlah_dibayar);
        $status_pembayaran = $jumlah_dibayar >= $total_akhir ? 'Lunas' : 'Hutang';

        // 3. UPDATE HEADER PEMBELIAN
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
          'metode_pembayaran' => $validatedData['metode_pembayaran'],
          'account_id' => $validatedData['account_id'] ?? null,
          'sisa_hutang' => $sisa,
          'status_pembayaran' => $statusBaru === 'Batal' ? 'Batal' : $status_pembayaran,
          'status_barang' => $statusBarangBaru,
          'catatan' => $validatedData['catatan'],
        ]);

        // 4. MANAJEMEN PEMBAYARAN
        if ($jumlah_dibayar > 0) {
          $pembayaranAwal = $pembelian->payments()->oldest('id')->first();
          $paymentData = [
            'tanggal_bayar' => $validatedData['tanggal'],
            'jumlah_bayar' => $jumlah_dibayar,
            'metode_pembayaran' => $validatedData['metode_pembayaran'],
            'account_id' => $validatedData['account_id'] ?? null,
            'catatan' => $status_pembayaran === 'Lunas' ? 'Revisi Pembayaran Lunas Awal' : 'Revisi Uang Muka (DP)',
          ];

          if ($pembayaranAwal) {
            $pembayaranAwal->update($paymentData);
          } else {
            $paymentData['user_id'] = Auth::id();
            $pembelian->payments()->create($paymentData);
          }
        }

        // 5. RE-INSERT DETAIL LALU KALKULASI HPP & STOK BARU
        $pembelian->details()->delete();

        foreach ($itemsForDetail as $itemData) {
          $variantId = $itemData['product_variant_id'] ?? null;
          $qty_baru = (int) $itemData['qty'];
          $harga_beli_form = (int) $itemData['harga_beli'];

          // A. Insert ke detail (snapshot struk pakai harga form)
          $pembelian->details()->create([
            'product_id' => $itemData['product_id'],
            'product_variant_id' => $variantId,
            'qty' => $qty_baru,
            'harga_beli' => $harga_beli_form,
            'diskon' => $itemData['diskon'] ?? 0,
            'taxe_id' => $itemData['taxe_id'] ?? null,
            'subtotal' => $itemData['subtotal'],
          ]);

          // B. Hitung HPP dan Injeksi Stok jika Diterima
          if ($statusBarangBaru === 'Diterima') {
            // --- Distribusi Beban untuk HPP Riil ---
            $dpp_item = $harga_beli_form * $qty_baru - (int) ($itemData['diskon'] ?? 0);
            $proporsi_item = $dpp_item / $subtotal_dpp_global;

            $taxe_id = $itemData['taxe_id'] ?? null;
            $pajak_rate = $taxe_id ? $taxesData->get($taxe_id)->rate ?? 0 : 0;
            $pajak_item = (int) round($dpp_item * ($pajak_rate / 100));

            $beban_global_item = $net_adjustment_global * $proporsi_item;
            $total_modal_item = $dpp_item + $pajak_item + $beban_global_item;

            $harga_beli_riil = $qty_baru > 0 ? (int) round($total_modal_item / $qty_baru) : $harga_beli_form;

            // --- Moving Average ---
            $queryStokLama = ProductStock::where('product_id', $itemData['product_id']);
            if ($variantId) {
              $queryStokLama->where('product_variant_id', $variantId);
            } else {
              $queryStokLama->whereNull('product_variant_id');
            }

            $total_stok_lama = (int) $queryStokLama->sum('qty');
            $total_stok_baru = $total_stok_lama + $qty_baru;

            if ($variantId && $variants->has($variantId)) {
              $varian = $variants->get($variantId);
              $old_hpp = (int) $varian->harga_beli;

              $nilai_aset_lama = $total_stok_lama * $old_hpp;
              $nilai_aset_baru = $qty_baru * $harga_beli_riil;
              $hpp_rata_rata = $total_stok_baru > 0 ? (int) round(($nilai_aset_lama + $nilai_aset_baru) / $total_stok_baru) : $harga_beli_riil;

              $varian->harga_beli = $hpp_rata_rata;
              $varian->save();
            } else {
              $produk = $products->get($itemData['product_id']);
              if ($produk) {
                $old_hpp = (int) $produk->harga_beli;

                $nilai_aset_lama = $total_stok_lama * $old_hpp;
                $nilai_aset_baru = $qty_baru * $harga_beli_riil;
                $hpp_rata_rata = $total_stok_baru > 0 ? (int) round(($nilai_aset_lama + $nilai_aset_baru) / $total_stok_baru) : $harga_beli_riil;

                $produk->harga_beli = $hpp_rata_rata;
                $produk->save();
              }
            }

            // --- Injeksi Stok Fisik ---
            $stockRecord = ProductStock::firstOrCreate(
              [
                'store_id' => $storeId,
                'product_id' => $itemData['product_id'],
                'product_variant_id' => $variantId,
              ],
              ['qty' => 0],
            );
            $stockRecord->increment('qty', $qty_baru);
          }
        }
      });

      return redirect()->route('pembelian.index')->with('success', 'Transaksi pembelian berhasil diperbarui.');
    } catch (\Exception $e) {
      return back()
        ->withInput()
        ->with('error', 'Gagal memperbarui transaksi: ' . $e->getMessage());
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
        'jumlah_bayar' => preg_replace('/[^0-9]/', '', $request->input('jumlah_bayar')),
      ]);
    }

    $validatedData = $request->validate(
      [
        'tanggal_bayar' => 'required|date',
        'jumlah_bayar' => 'required|numeric|min:1|max:' . $pembelian->sisa_hutang,
        'metode_pembayaran' => 'required|in:TUNAI,TRANSFER,QRIS',
        'account_id' => 'required_if:metode_pembayaran,TRANSFER|nullable|exists:accounts,id',
        'referensi_pembayaran' => 'nullable|string|max:100',
        'catatan' => 'nullable|string',
      ],
      [
        'jumlah_bayar.max' => 'Jumlah pembayaran tidak boleh melebihi sisa hutang (Rp ' . number_format($pembelian->sisa_hutang, 0, ',', '.') . ').',
        'account_id.required_if' => 'Rekening tujuan wajib dipilih jika menggunakan metode TRANSFER.',
      ],
    );

    try {
      DB::transaction(function () use ($validatedData, $pembelian) {
        // 1. Masukkan data ke tabel purchase_payments melalui relasi
        $pembelian->payments()->create([
          'user_id' => Auth::id(),
          'tanggal_bayar' => $validatedData['tanggal_bayar'],
          'jumlah_bayar' => $validatedData['jumlah_bayar'],
          'metode_pembayaran' => $validatedData['metode_pembayaran'],
          'account_id' => $validatedData['account_id'] ?? null,
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
          'status_pembayaran' => $statusPembayaranBaru,
        ]);
      });

      return redirect()->route('pembelian.show', $pembelian->referensi)->with('success', 'Pembayaran cicilan berhasil dicatat.');
    } catch (\Exception $e) {
      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }
}
