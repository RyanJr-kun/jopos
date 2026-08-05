<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Supplier;
use App\Models\CashFlow;
use App\Models\Customer;
use Modules\Inventory\Models\Purchase;
use Modules\POS\Models\Sale;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Exports\LabaRugiExport;
use App\Exports\PurchaseExport;
use App\Exports\SaleExport;
use App\Exports\InventarisExport;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormats;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
  /**
   * Menampilkan laporan pergerakan inventaris.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function inventaris(Request $request)
  {
    $storeId = $request->input('store_id');

    // ========================================================
    // Query 1: Purchase (Stock Masuk)
    // ========================================================
    $pembelian = DB::table('purchase_items')
      ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
      ->join('products', 'purchase_items.product_id', '=', 'products.id')
      ->leftJoin('product_variants', 'purchase_items.product_variant_id', '=', 'product_variants.id')
      ->where('purchases.status_barang', 'Diterima')
      ->when($storeId, fn($q) => $q->where('purchases.store_id', $storeId))
      ->select(
        'purchases.tanggal_pembelian as tanggal',
        'products.id as product_id',
        'products.name_product',
        DB::raw('COALESCE(product_variants.sku, products.sku) as sku'),
        // Sub-query untuk menggabungkan nama varian (cth: "Merah - 8GB")
        DB::raw('(SELECT GROUP_CONCAT(product_variant_options.value SEPARATOR " - ") 
                  FROM product_variant_option_pivot 
                  JOIN product_variant_options ON product_variant_option_pivot.product_variant_option_id = product_variant_options.id 
                  WHERE product_variant_option_pivot.product_variant_id = product_variants.id) as nama_varian'),
        DB::raw("'Purchase' as tipe_gerakan"),
        'purchases.referensi',
        'purchase_items.qty as jumlah_masuk',
        DB::raw('0 as jumlah_keluar'),
        'purchases.catatan as keterangan',
        DB::raw("'pembelian.show' as route_name"),
        'purchases.referensi as referensi_id',
      );

    // ========================================================
    // Query 2: Sale (Stock Keluar)
    // ========================================================
    $penjualan = DB::table('sale_items')
      ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
      ->join('products', 'sale_items.product_id', '=', 'products.id')
      ->leftJoin('product_variants', 'sale_items.product_variant_id', '=', 'product_variants.id')
      ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
      ->when($storeId, fn($q) => $q->where('sales.store_id', $storeId))
      ->select(
        'sales.tanggal_penjualan as tanggal',
        'products.id as product_id',
        'products.name_product',
        DB::raw('COALESCE(product_variants.sku, products.sku) as sku'),
        DB::raw('(SELECT GROUP_CONCAT(product_variant_options.value SEPARATOR " - ") 
                  FROM product_variant_option_pivot 
                  JOIN product_variant_options ON product_variant_option_pivot.product_variant_option_id = product_variant_options.id 
                  WHERE product_variant_option_pivot.product_variant_id = product_variants.id) as nama_varian'),
        DB::raw("'Sale' as tipe_gerakan"),
        'sales.referensi',
        DB::raw('0 as jumlah_masuk'),
        'sale_items.jumlah as jumlah_keluar',
        'sales.catatan as keterangan',
        DB::raw("'penjualan.show' as route_name"),
        'sales.referensi as referensi_id',
      );

    // ========================================================
    // Query 3: Stock Opname (Masuk/Keluar)
    // ========================================================
    $opname = DB::table('stock_opname_items')
      ->join('stock_opnames', 'stock_opname_items.stock_opname_id', '=', 'stock_opnames.id')
      ->join('products', 'stock_opname_items.product_id', '=', 'products.id')
      ->leftJoin('product_variants', 'stock_opname_items.product_variant_id', '=', 'product_variants.id')
      ->where('stock_opname_items.selisih', '!=', 0)
      ->when($storeId, fn($q) => $q->where('stock_opnames.store_id', $storeId))
      ->select(
        'stock_opnames.tanggal_opname as tanggal',
        'products.id as product_id',
        'products.name_product',
        DB::raw('COALESCE(product_variants.sku, products.sku) as sku'),
        DB::raw('(SELECT GROUP_CONCAT(product_variant_options.value SEPARATOR " - ") 
                  FROM product_variant_option_pivot 
                  JOIN product_variant_options ON product_variant_option_pivot.product_variant_option_id = product_variant_options.id 
                  WHERE product_variant_option_pivot.product_variant_id = product_variants.id) as nama_varian'),
        DB::raw("'Stock Opname' as tipe_gerakan"),
        'stock_opnames.kode_opname as referensi',
        DB::raw('CASE WHEN stock_opname_items.selisih > 0 THEN stock_opname_items.selisih ELSE 0 END as jumlah_masuk'),
        DB::raw('CASE WHEN stock_opname_items.selisih < 0 THEN ABS(stock_opname_items.selisih) ELSE 0 END as jumlah_keluar'),
        'stock_opname_items.keterangan',
        DB::raw("'stok-opname.show' as route_name"),
        'stock_opnames.kode_opname as referensi_id',
      );

    // ========================================================
    // Query 4: Penyesuaian Stock (Masuk/Keluar)
    // ========================================================
    $penyesuaian = DB::table('stock_adjustment_items')
      ->join('stock_adjustments', 'stock_adjustment_items.stock_adjustment_id', '=', 'stock_adjustments.id')
      ->join('products', 'stock_adjustment_items.product_id', '=', 'products.id')
      ->leftJoin('product_variants', 'stock_adjustment_items.product_variant_id', '=', 'product_variants.id')
      ->when($storeId, fn($q) => $q->where('stock_adjustments.store_id', $storeId))
      ->select(
        'stock_adjustments.tanggal_penyesuaian as tanggal',
        'products.id as product_id',
        'products.name_product',
        DB::raw('COALESCE(product_variants.sku, products.sku) as sku'),
        DB::raw('(SELECT GROUP_CONCAT(product_variant_options.value SEPARATOR " - ") 
                  FROM product_variant_option_pivot 
                  JOIN product_variant_options ON product_variant_option_pivot.product_variant_option_id = product_variant_options.id 
                  WHERE product_variant_option_pivot.product_variant_id = product_variants.id) as nama_varian'),
        DB::raw("'Penyesuaian' as tipe_gerakan"),
        'stock_adjustments.kode_penyesuaian as referensi',
        DB::raw("CASE WHEN stock_adjustment_items.type = 'IN' THEN stock_adjustment_items.jumlah ELSE 0 END as jumlah_masuk"),
        DB::raw("CASE WHEN stock_adjustment_items.type = 'OUT' THEN stock_adjustment_items.jumlah ELSE 0 END as jumlah_keluar"),
        'stock_adjustment_items.alasan as keterangan',
        DB::raw("'stok-penyesuaian.show' as route_name"),
        'stock_adjustments.kode_penyesuaian as referensi_id',
      );

    // Gabungkan semua query
    $unionQuery = $penyesuaian->unionAll($opname)->unionAll($penjualan)->unionAll($pembelian);

    // Buat query baru dari hasil union untuk bisa diurutkan dan difilter
    $query = DB::query()->fromSub($unionQuery, 'stock_movements');

    // Terapkan filter
    if ($request->filled('product_id')) {
      $query->where('stock_movements.product_id', $request->product_id);
    }
    if ($request->filled('tipe_gerakan')) {
      $query->where('stock_movements.tipe_gerakan', $request->tipe_gerakan);
    }
    if ($request->filled('start_date') && $request->filled('end_date')) {
      $query->whereBetween('stock_movements.tanggal', [$request->start_date, $request->end_date]);
    }

    // Clone query untuk menghitung total sebelum paginasi
    $totalQuery = clone $query;
    $summary = $totalQuery->selectRaw('SUM(jumlah_masuk) as total_masuk, SUM(jumlah_keluar) as total_keluar')->first();

    $formattedProducts = collect();

    // Tarik produk beserta varian dan opsi-opsinya melalui relasi
    $products = Product::with(['variants.options'])
      ->orderBy('name_product')
      ->get();

    foreach ($products as $produk) {
      if ($produk->variants->count() > 0) {
        foreach ($produk->variants as $variant) {
          // Gabungkan nilai opsi (contoh: "Merah - 8GB - 256GB")
          $variantNames = $variant->options->pluck('value')->implode(' - ');

          $formattedProducts->push(
            (object) [
              'id' => $produk->id, // Tetap gunakan ID Produk induk
              'display_name' => $produk->name_product . ' - ' . $variantNames . ' (' . $variant->sku . ')',
            ],
          );
        }
      } else {
        $formattedProducts->push(
          (object) [
            'id' => $produk->id,
            'display_name' => $produk->name_product . ' (' . $produk->sku . ')',
          ],
        );
      }
    }

    $pergerakan = $query->orderBy('tanggal', 'desc')->paginate(50)->withQueryString();

    return view('content.dashboard.laporan.laporan-inventaris', [
      'title' => 'Laporan Pergerakan Inventaris',
      'pergerakan' => $pergerakan,
      'summary' => (object) [
        'total_produk' => Product::count(),
        'total_stok' => ProductStock::sum('qty'),
        'total_masuk' => $summary->total_masuk ?? 0,
        'total_keluar' => $summary->total_keluar ?? 0,
      ],
      // GANTI DI SINI: Kirim collection yang sudah di-format di atas
      'products' => $formattedProducts,
      'tipe_gerakan_options' => ['Purchase', 'Sale', 'Stock Opname', 'Penyesuaian'],
      'stores' => Store::orderBy('name_toko')->get(['id', 'name_toko']),
    ]);
  }

  /**
   * Menangani ekspor laporan pergerakan inventaris ke Excel.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function exportInventaris(Request $request)
  {
    // PERUBAHAN
    $profilToko = Store::first();
    $type = $request->query('type', 'xlsx');

    // --- REUSEABLE QUERY LOGIC ---
    $baseQuery = function (Request $request) {
      $storeId = $request->input('store_id');

      // Query 1: Purchase (Stock Masuk)
      $pembelian = DB::table('purchase_items')
        ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
        ->join('products', 'purchase_items.product_id', '=', 'products.id')
        ->where('purchases.status_barang', 'Diterima')
        ->when($storeId, fn($q) => $q->where('purchases.store_id', $storeId))
        ->select(
          'purchases.tanggal_pembelian as tanggal',
          'products.id as product_id',
          'products.name_product',
          'products.sku',
          DB::raw("'Purchase' as tipe_gerakan"),
          'purchases.referensi',
          'purchase_items.qty as jumlah_masuk',
          DB::raw('0 as jumlah_keluar'),
          'purchases.catatan as keterangan',
        );

      // Query 2: Sale (Stock Keluar)
      $penjualan = DB::table('sale_items')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
        ->when($storeId, fn($q) => $q->where('sales.store_id', $storeId))
        ->select(
          'sales.tanggal_penjualan as tanggal',
          'products.id as product_id',
          'products.name_product',
          'products.sku',
          DB::raw("'Sale' as tipe_gerakan"),
          'sales.referensi',
          DB::raw('0 as jumlah_masuk'),
          'sale_items.jumlah as jumlah_keluar',
          'sales.catatan as keterangan',
        );

      // Query 3: Stock Opname (Masuk/Keluar)
      $opname = DB::table('stock_opname_items')
        ->join('stock_opnames', 'stock_opname_items.stock_opname_id', '=', 'stock_opnames.id')
        ->join('products', 'stock_opname_items.product_id', '=', 'products.id')
        ->where('stock_opname_items.selisih', '!=', 0)
        ->when($storeId, fn($q) => $q->where('stock_opnames.store_id', $storeId))
        ->select(
          'stock_opnames.tanggal_opname as tanggal',
          'products.id as product_id',
          'products.name_product',
          'products.sku',
          DB::raw("'Stock Opname' as tipe_gerakan"),
          'stock_opnames.kode_opname as referensi',
          DB::raw('CASE WHEN stock_opname_items.selisih > 0 THEN stock_opname_items.selisih ELSE 0 END as jumlah_masuk'),
          DB::raw('CASE WHEN stock_opname_items.selisih < 0 THEN ABS(stock_opname_items.selisih) ELSE 0 END as jumlah_keluar'),
          'stock_opname_items.keterangan',
        );

      // Query 4: Penyesuaian Stock (Masuk/Keluar)
      $penyesuaian = DB::table('stock_adjustment_items')
        ->join('stock_adjustments', 'stock_adjustment_items.stock_adjustment_id', '=', 'stock_adjustments.id')
        ->join('products', 'stock_adjustment_items.product_id', '=', 'products.id')
        ->when($storeId, fn($q) => $q->where('stock_adjustments.store_id', $storeId))
        ->select(
          'stock_adjustments.tanggal_penyesuaian as tanggal',
          'products.id as product_id',
          'products.name_product',
          'products.sku',
          DB::raw("'Penyesuaian' as tipe_gerakan"),
          'stock_adjustments.kode_penyesuaian as referensi',
          DB::raw("CASE WHEN stock_adjustment_items.type = 'IN' THEN stock_adjustment_items.jumlah ELSE 0 END as jumlah_masuk"),
          DB::raw("CASE WHEN stock_adjustment_items.type = 'OUT' THEN stock_adjustment_items.jumlah ELSE 0 END as jumlah_keluar"),
          'stock_adjustment_items.alasan as keterangan',
        );

      // Gabungkan semua query
      $unionQuery = $penyesuaian->unionAll($opname)->unionAll($penjualan)->unionAll($pembelian);

      // Buat query baru dari hasil union untuk bisa diurutkan dan difilter
      $query = DB::query()->fromSub($unionQuery, 'stock_movements');

      // Terapkan filter
      if ($request->filled('product_id')) {
        $query->where('stock_movements.product_id', $request->product_id);
      }
      if ($request->filled('tipe_gerakan')) {
        $query->where('stock_movements.tipe_gerakan', $request->tipe_gerakan);
      }
      if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('stock_movements.tanggal', [$request->start_date, $request->end_date]);
      }
      return $query;
    };
    // --- END REUSEABLE QUERY LOGIC ---

    $query = $baseQuery($request);
    $pergerakan = $query->orderBy('tanggal', 'desc')->get();

    // Tentukan tanggal default jika tidak ada filter
    $startDate = $request->input('start_date', $pergerakan->min('tanggal') ?? now()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', $pergerakan->max('tanggal') ?? now()->endOfMonth()->toDateString());

    $data = [
      'title' => 'Laporan Pergerakan Inventaris',
      'profilToko' => $profilToko,
      'startDate' => $startDate,
      'endDate' => $endDate,
      'pergerakan' => $pergerakan,
    ];

    $fileName = 'laporan-inventaris-' . now()->format('Y-m-d_H-i-s') . '.' . $type;

    if ($type === 'pdf') {
      $pdf = Pdf::loadView('content.dashboard.laporan.pdf.export-inventaris', $data);
      return $pdf->download($fileName);
    }

    return Excel::download(new InventarisExport($pergerakan), $fileName, ExcelFormats::XLSX);
  }

  /**
   * Menampilkan laporan pembelian.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function pembelian(Request $request)
  {
    $query = Purchase::with(['supplier', 'user'])->latest('tanggal_pembelian'); // ->select() tidak diperlukan, Eloquent akan memilih semua kolom secara default.

    // Terapkan filter
    if ($request->filled('store_id')) {
      $query->where('store_id', $request->store_id);
    }
    if ($request->filled('supplier_id')) {
      $query->where('supplier_id', $request->supplier_id);
    }
    if ($request->filled('status_pembayaran')) {
      $query->where('status_pembayaran', $request->status_pembayaran);
    }
    if ($request->filled('status_barang')) {
      $query->where('status_barang', $request->status_barang);
    }
    if ($request->filled('start_date') && $request->filled('end_date')) {
      $query->whereBetween('tanggal_pembelian', [$request->start_date, $request->end_date]);
    }

    // Clone query untuk menghitung total sebelum paginasi
    $totalQuery = clone $query;
    $totalItemsQuery = clone $query;

    $totals = $totalQuery
      ->reorder()
      ->selectRaw(
        '
            SUM(total_akhir) as grand_total,
            COUNT(id) as total_transactions
        ',
      )
      ->first();

    // Hitung total produk yang diterima dari transaksi yang sudah difilter
    $totals->total_products_received = $totalItemsQuery
      ->where('status_barang', 'Diterima') // Hanya hitung yang statusnya diterima
      ->join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
      ->sum('purchase_items.qty');

    // Hitung total hutang (opsional, jika masih ingin digunakan di tempat lain)
    $totals->total_due = $totalQuery->sum(DB::raw('total_akhir - jumlah_dibayar'));

    // Lakukan paginasi
    $purchases = $query->paginate(50)->withQueryString();

    return view('content.dashboard.laporan.laporan-pembelian', [
      'title' => 'Laporan Purchase',
      'purchases' => $purchases,
      'suppliers' => Supplier::orderBy('name', 'asc')->get(['id', 'name']),
      'statusPembayaranOptions' => ['Lunas', 'Hutang', 'Batal'],
      'statusBarangOptions' => ['Diterima', 'Belum Diterima', 'Dibatalkan'],
      'totals' => $totals,
      'stores' => Store::orderBy('name_toko', 'asc')->get(['id', 'name_toko']),
    ]);
  }

  /**
   * Menangani ekspor laporan pembelian ke Excel atau PDF.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function exportPurchase(Request $request)
  {
    $profilToko = Store::first();
    $type = $request->query('type', 'xlsx');

    // Gunakan query yang sama dengan method pembelian() untuk konsistensi filter
    $query = Purchase::with(['supplier', 'user'])->latest('tanggal_pembelian');

    // Terapkan filter
    if ($request->filled('store_id')) {
      $query->where('store_id', $request->store_id);
    }
    if ($request->filled('supplier_id')) {
      $query->where('supplier_id', $request->supplier_id);
    }
    if ($request->filled('status_pembayaran')) {
      $query->where('status_pembayaran', $request->status_pembayaran);
    }
    if ($request->filled('status_barang')) {
      $query->where('status_barang', $request->status_barang);
    }
    if ($request->filled('start_date') && $request->filled('end_date')) {
      $query->whereBetween('tanggal_pembelian', [$request->start_date, $request->end_date]);
    }

    // Ambil semua data yang cocok tanpa paginasi
    $purchases = $query->get();

    // Siapkan data untuk diteruskan ke view/export class
    $data = [
      'title' => 'Laporan Purchase',
      'profilToko' => $profilToko,
      'startDate' => $request->input('start_date', $purchases->min('tanggal_pembelian')),
      'endDate' => $request->input('end_date', $purchases->max('tanggal_pembelian')),
      'purchases' => $purchases,
    ];

    $fileName = 'laporan-pembelian-' . now()->format('Y-m-d_H-i-s') . '.' . $type;

    if ($type === 'pdf') {
      $pdf = Pdf::loadView('content.dashboard.laporan.pdf.export-pembelian', $data);
      return $pdf->download($fileName);
    }
    return Excel::download(new PurchaseExport($purchases), $fileName, ExcelFormats::XLSX);
  }
  /**
   * Menampilkan laporan penjualan.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function penjualan(Request $request)
  {
    $query = Sale::with(['customer', 'user'])->latest('tanggal_penjualan'); // ->select() tidak diperlukan, Eloquent akan memilih semua kolom secara default.

    // Terapkan filter
    if ($request->filled('store_id')) {
      $query->where('store_id', $request->store_id);
    }
    if ($request->filled('customer_id')) {
      $query->where('customer_id', $request->customer_id);
    }
    if ($request->filled('status_pembayaran')) {
      $query->where('status_pembayaran', $request->status_pembayaran);
    }
    if ($request->filled('start_date') && $request->filled('end_date')) {
      $query->whereBetween('tanggal_penjualan', [$request->start_date, $request->end_date]);
    }

    // Clone query untuk menghitung total sebelum paginasi
    $totalQuery = clone $query;
    $totalItemsQuery = clone $query;

    $totals = $totalQuery
      ->reorder()
      ->selectRaw(
        '
            SUM(total_akhir) as grand_total,
            COUNT(id) as total_transactions
        ',
      )
      ->first();

    // Hitung total item terjual dari transaksi yang sudah difilter
    $totals->total_products_sold = $totalItemsQuery->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')->sum('sale_items.jumlah');

    // Lakukan paginasi
    $sales = $query->paginate(50)->withQueryString();

    return view('content.dashboard.laporan.laporan-penjualan', [
      'title' => 'Laporan Sale',
      'sales' => $sales,
      'customers' => Customer::orderBy('name', 'asc')->get(['id', 'name']),
      'statusPembayaranOptions' => ['Lunas', 'Piutang', 'Batal'],
      'totals' => $totals,
      'stores' => Store::orderBy('name_toko', 'asc')->get(['id', 'name_toko']),
    ]);
  }

  /**
   * Menangani ekspor laporan penjualan ke Excel atau PDF.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function exportSale(Request $request)
  {
    $profilToko = Store::first();
    $type = $request->query('type', 'xlsx');

    // Gunakan query yang sama dengan method penjualan() untuk konsistensi filter
    $query = Sale::with(['customer', 'user'])->latest('tanggal_penjualan');

    // Terapkan filter
    if ($request->filled('store_id')) {
      $query->where('store_id', $request->store_id);
    }
    if ($request->filled('customer_id')) {
      $query->where('customer_id', $request->customer_id);
    }
    if ($request->filled('status_pembayaran')) {
      $query->where('status_pembayaran', $request->status_pembayaran);
    }
    if ($request->filled('start_date') && $request->filled('end_date')) {
      $query->whereBetween('tanggal_penjualan', [$request->start_date, $request->end_date]);
    }

    // Ambil semua data yang cocok tanpa paginasi
    $sales = $query->get();

    // Hitung total berdasarkan data yang sudah difilter
    $grand_total = $sales->sum('total_akhir');
    $total_paid = $sales->sum('jumlah_dibayar');
    $total_due = $sales->sum('sisa_pembayaran');

    // Tentukan tanggal default jika tidak ada filter
    $startDate = $request->input('start_date', $sales->min('tanggal_penjualan') ?? now()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', $sales->max('tanggal_penjualan') ?? now()->endOfMonth()->toDateString());

    $data = [
      'title' => 'Laporan Sale',
      'profilToko' => $profilToko,
      'startDate' => $startDate,
      'endDate' => $endDate,
      'sales' => $sales,
      'totals' => (object) [
        'grand_total' => $grand_total,
        'total_paid' => $total_paid,
        'total_due' => $total_due,
      ],
    ];

    $fileName = 'laporan-penjualan-' . now()->format('Y-m-d_H-i-s') . '.' . $type;

    if ($type === 'pdf') {
      $pdf = Pdf::loadView('content.dashboard.laporan.pdf.export-penjualan', $data);
      return $pdf->download($fileName);
    }

    return Excel::download(new SaleExport($sales), $fileName, ExcelFormats::XLSX);
  }

  /**
   * Menampilkan laporan laba rugi.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function labaRugi(Request $request)
  {
    // 1. Atur rentang tanggal, defaultnya bulan ini
    $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
    $storeId = $request->input('store_id');

    // 2. Hitung Total Pendapatan dari Sale (yang tidak dibatalkan)
    $totalRevenue = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
      ->where('status_pembayaran', '!=', 'Dibatalkan')
      ->when($storeId, fn($q) => $q->where('store_id', $storeId))
      ->sum('total_akhir');

    // Tambahan: Hitung Total Pendapatan Lain-lain dari CashFlow
    $totalOtherIncome = CashFlow::income()
      ->aktif()
      ->where('source_type', '!=', 'sale_payment')
      ->whereBetween('tanggal', [$startDate, $endDate])
      ->when($storeId, fn($q) => $q->where('store_id', $storeId))
      ->sum('nominal');

    // 3. Hitung Harga Pokok Sale (HPP / COGS)
    // HPP = Jumlah barang terjual * harga beli SAAT TRANSAKSI (dari sale_items)
    // PENTING: Menggunakan sale_items.harga_beli (snapshot saat penjualan),
    // BUKAN products.harga_beli (harga terkini yang bisa berubah kapan saja).
    $cogs = DB::table('sale_items')
      ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
      ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
      ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
      ->when($storeId, fn($q) => $q->where('sales.store_id', $storeId))
      ->sum(DB::raw('sale_items.jumlah * sale_items.harga_beli'));

    // 4. Hitung Laba Kotor (Pendapatan - HPP)
    $grossProfit = $totalRevenue - $cogs;

    // 5. Hitung Total Beban Operasional dari CashFlow
    $totalExpenses = CashFlow::expense()
      ->aktif()
      ->where('source_type', '!=', 'purchase_payment')
      ->whereBetween('tanggal', [$startDate, $endDate])
      ->when($storeId, fn($q) => $q->where('store_id', $storeId))
      ->sum('nominal');

    // 6. Hitung Laba Bersih (Laba Kotor - Beban Operasional)
    $netProfit = $grossProfit + $totalOtherIncome - $totalExpenses;

    // 7. Siapkan data untuk grafik 6 bulan terakhir
    $chartLabels = [];
    $chartNetProfits = [];

    for ($i = 5; $i >= 0; $i--) {
      $date = now()->subMonths($i);
      $monthStart = $date->startOfMonth()->toDateString();
      $monthEnd = $date->endOfMonth()->toDateString();

      // Pendapatan bulan ini
      $monthlyRevenue = Sale::whereBetween('tanggal_penjualan', [$monthStart, $monthEnd])
        ->where('status_pembayaran', '!=', 'Dibatalkan')
        ->when($storeId, fn($q) => $q->where('store_id', $storeId))
        ->sum('total_akhir');

      // HPP bulan ini (menggunakan harga beli snapshot dari sale_items)
      $monthlyCogs = DB::table('sale_items')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->whereBetween('sales.tanggal_penjualan', [$monthStart, $monthEnd])
        ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
        ->when($storeId, fn($q) => $q->where('sales.store_id', $storeId))
        ->sum(DB::raw('sale_items.jumlah * sale_items.harga_beli'));

      // Income lain-lain bulan ini
      $monthlyOtherIncome = CashFlow::income()
        ->aktif()
        ->whereBetween('tanggal', [$monthStart, $monthEnd])
        ->when($storeId, fn($q) => $q->where('store_id', $storeId))
        ->sum('nominal');

      // Beban bulan ini
      $monthlyExpenses = CashFlow::expense()
        ->aktif()
        ->whereBetween('tanggal', [$monthStart, $monthEnd])
        ->when($storeId, fn($q) => $q->where('store_id', $storeId))
        ->sum('nominal');

      // Laba bersih bulan ini
      $monthlyGrossProfit = $monthlyRevenue - $monthlyCogs;
      $monthlyNetProfit = $monthlyGrossProfit + $monthlyOtherIncome - $monthlyExpenses;

      // Tambahkan ke array untuk dikirim ke view
      $chartLabels[] = $date->isoFormat('MMMM Y');
      $chartNetProfits[] = $monthlyNetProfit;
    }

    return view('content.dashboard.laporan.laporan-laba-rugi', [
      'title' => 'Laporan Laba Rugi',
      'startDate' => $startDate,
      'endDate' => $endDate,
      'totalRevenue' => $totalRevenue,
      'totalOtherIncome' => $totalOtherIncome,
      'cogs' => $cogs,
      'grossProfit' => $grossProfit,
      'totalExpenses' => $totalExpenses,
      'netProfit' => $netProfit,
      'chartLabels' => $chartLabels,
      'chartNetProfits' => $chartNetProfits,
      'stores' => Store::orderBy('name_toko')->get(['id', 'name_toko']),
    ]);
  }

  /**
   * Menangani ekspor laporan laba rugi ke PDF.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function exportLabaRugi(Request $request)
  {
    $profilToko = Store::first();
    // 1. Atur rentang tanggal dari request
    $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
    $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
    $storeId = $request->input('store_id');

    // 2. Hitung Total Pendapatan
    $totalRevenue = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
      ->where('status_pembayaran', '!=', 'Dibatalkan')
      ->when($storeId, fn($q) => $q->where('store_id', $storeId))
      ->sum('total_akhir');

    // Tambahan: Hitung Total Pendapatan Lain-lain
    $totalOtherIncome = CashFlow::income()
      ->aktif()
      ->whereBetween('tanggal', [$startDate, $endDate])
      ->when($storeId, fn($q) => $q->where('store_id', $storeId))
      ->sum('nominal');

    // 3. Hitung HPP (menggunakan harga beli snapshot dari sale_items)
    $cogs = DB::table('sale_items')
      ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
      ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
      ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
      ->when($storeId, fn($q) => $q->where('sales.store_id', $storeId))
      ->sum(DB::raw('sale_items.jumlah * sale_items.harga_beli'));

    // 4. Hitung Laba Kotor
    $grossProfit = $totalRevenue - $cogs;

    // 5. Hitung Total Beban
    $totalExpenses = CashFlow::expense()
      ->aktif()
      ->whereBetween('tanggal', [$startDate, $endDate])
      ->when($storeId, fn($q) => $q->where('store_id', $storeId))
      ->sum('nominal');

    // 6. Hitung Laba Bersih
    $netProfit = $grossProfit + $totalOtherIncome - $totalExpenses;

    // 7. Siapkan data untuk view PDF
    $pdf = Pdf::loadView('content.dashboard.laporan.pdf.export-laba', [
      'title' => 'Laporan Laba Rugi',
      'profilToko' => $profilToko,
      'startDate' => $startDate,
      'endDate' => $endDate,
      'totalRevenue' => $totalRevenue,
      'totalOtherIncome' => $totalOtherIncome,
      'cogs' => $cogs,
      'grossProfit' => $grossProfit,
      'totalExpenses' => $totalExpenses,
      'netProfit' => $netProfit,
    ]);

    $fileName = 'laporan-laba-rugi-' . now()->format('Y-m-d') . '.pdf';
    return $pdf->download($fileName);
  }
}
