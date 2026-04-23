<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Income;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Stores;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Exports\LabaRugiExport;
use App\Exports\PurchaseExport;
use App\Exports\SaleExport;
use App\Exports\InventarisExport;
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
        // Query 1: Purchase (Stock Masuk)
        $pembelian = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->where('purchases.status_barang', 'Diterima')
            ->select(
                'purchases.tanggal_pembelian as tanggal',
                'products.id as product_id',
                'products.name_product',
                'products.sku',
                DB::raw("'Purchase' as tipe_gerakan"),
                'purchases.referensi',
                'purchase_items.qty as jumlah_masuk', // FIX: Menggunakan kolom 'qty'
                DB::raw("0 as jumlah_keluar"),
                'purchases.catatan as keterangan',
                DB::raw("'pembelian.show' as route_name"),
                'purchases.referensi as referensi_id'
            );

        // Query 2: Sale (Stock Keluar)
        $penjualan = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->select(
                'sales.tanggal_penjualan as tanggal',
                'products.id as product_id',
                'products.name_product',
                'products.sku',
                DB::raw("'Sale' as tipe_gerakan"),
                'sales.referensi',
                DB::raw("0 as jumlah_masuk"),
                'sale_items.jumlah as jumlah_keluar',
                'sales.catatan as keterangan',
                DB::raw("'penjualan.show' as route_name"),
                'sales.referensi as referensi_id'
            );

        // Query 3: Stock Opname (Masuk/Keluar)
        $opname = DB::table('stock_take_items')
            ->join('stock_takes', 'stock_take_items.stock_take_id', '=', 'stock_takes.id')
            ->join('products', 'stock_take_items.product_id', '=', 'products.id')
            ->where('stock_take_items.selisih', '!=', 0)
            ->select(
                'stock_takes.tanggal_opname as tanggal',
                'products.id as product_id',
                'products.name_product',
                'products.sku',
                DB::raw("'Stock Opname' as tipe_gerakan"),
                'stock_takes.kode_opname as referensi',
                DB::raw("CASE WHEN stock_take_items.selisih > 0 THEN stock_take_items.selisih ELSE 0 END as jumlah_masuk"),
                DB::raw("CASE WHEN stock_take_items.selisih < 0 THEN ABS(stock_take_items.selisih) ELSE 0 END as jumlah_keluar"),
                'stock_take_items.keterangan',
                DB::raw("'stok-opname.show' as route_name"),
                'stock_takes.kode_opname as referensi_id'
            );

        // Query 4: Penyesuaian Stock (Masuk/Keluar)
        $penyesuaian = DB::table('stock_adjustment_items')
            ->join('stock_adjustments', 'stock_adjustment_items.stock_adjustment_id', '=', 'stock_adjustments.id')
            ->join('products', 'stock_adjustment_items.product_id', '=', 'products.id')
            ->select(
                'stock_adjustments.tanggal_penyesuaian as tanggal',
                'products.id as product_id',
                'products.name_product',
                'products.sku',
                DB::raw("'Penyesuaian' as tipe_gerakan"),
                'stock_adjustments.kode_penyesuaian as referensi',
                DB::raw("CASE WHEN stock_adjustment_items.tipe = 'IN' THEN stock_adjustment_items.jumlah ELSE 0 END as jumlah_masuk"),
                DB::raw("CASE WHEN stock_adjustment_items.tipe = 'OUT' THEN stock_adjustment_items.jumlah ELSE 0 END as jumlah_keluar"),
                'stock_adjustment_items.alasan as keterangan',
                DB::raw("'stok-penyesuaian.show' as route_name"),
                'stock_adjustments.kode_penyesuaian as referensi_id' // FIX: Gunakan kode_penyesuaian agar URL konsisten
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

        // Urutkan dan lakukan paginasi
        $pergerakan = $query->orderBy('tanggal', 'desc')->paginate(50)->withQueryString();

        return view('content.laporan.laporan-inventaris', [
            'title' => 'Laporan Pergerakan Inventaris',
            'pergerakan' => $pergerakan,
            'summary' => (object) [
                'total_produk' => Product::count(),
                'total_stok' => Product::sum('qty'),
                'total_masuk' => $summary->total_masuk ?? 0,
                'total_keluar' => $summary->total_keluar ?? 0,
            ],
            'products' => Product::orderBy('name_product')->get(['id', 'name_product']),
            'tipe_gerakan_options' => ['Purchase', 'Sale', 'Stock Opname', 'Penyesuaian'],
        ]);
    }

    /**
     * Menangani ekspor laporan pergerakan inventaris ke Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportInventaris(Request $request) // PERUBAHAN
    {
        $profilToko = Stores::first();
        $type = $request->query('type', 'xlsx');

        // --- REUSEABLE QUERY LOGIC ---
        $baseQuery = function (Request $request) {
            // Query 1: Purchase (Stock Masuk)
            $pembelian = DB::table('purchase_items')
                ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
                ->join('products', 'purchase_items.product_id', '=', 'products.id')
                ->where('purchases.status_barang', 'Diterima')
                ->select(
                    'purchases.tanggal_pembelian as tanggal',
                    'products.id as product_id',
                    'products.name_product',
                    'products.sku',
                    DB::raw("'Purchase' as tipe_gerakan"),
                    'purchases.referensi',
                    'purchase_items.qty as jumlah_masuk',
                    DB::raw("0 as jumlah_keluar"),
                    'purchases.catatan as keterangan'
                );

            // Query 2: Sale (Stock Keluar)
            $penjualan = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
                ->select(
                    'sales.tanggal_penjualan as tanggal',
                    'products.id as product_id',
                    'products.name_product',
                    'products.sku',
                    DB::raw("'Sale' as tipe_gerakan"),
                    'sales.referensi',
                    DB::raw("0 as jumlah_masuk"),
                    'sale_items.jumlah as jumlah_keluar',
                    'sales.catatan as keterangan'
                );

            // Query 3: Stock Opname (Masuk/Keluar)
            $opname = DB::table('stock_take_items')
                ->join('stock_takes', 'stock_take_items.stock_take_id', '=', 'stock_takes.id')
                ->join('products', 'stock_take_items.product_id', '=', 'products.id')
                ->where('stock_take_items.selisih', '!=', 0)
                ->select(
                    'stock_takes.tanggal_opname as tanggal',
                    'products.id as product_id',
                    'products.name_product',
                    'products.sku',
                    DB::raw("'Stock Opname' as tipe_gerakan"),
                    'stock_takes.kode_opname as referensi',
                    DB::raw("CASE WHEN stock_take_items.selisih > 0 THEN stock_take_items.selisih ELSE 0 END as jumlah_masuk"),
                    DB::raw("CASE WHEN stock_take_items.selisih < 0 THEN ABS(stock_take_items.selisih) ELSE 0 END as jumlah_keluar"),
                    'stock_take_items.keterangan'
                );

            // Query 4: Penyesuaian Stock (Masuk/Keluar)
            $penyesuaian = DB::table('stock_adjustment_items')
                ->join('stock_adjustments', 'stock_adjustment_items.stock_adjustment_id', '=', 'stock_adjustments.id')
                ->join('products', 'stock_adjustment_items.product_id', '=', 'products.id')
                ->select(
                    'stock_adjustments.tanggal_penyesuaian as tanggal',
                    'products.id as product_id',
                    'products.name_product',
                    'products.sku',
                    DB::raw("'Penyesuaian' as tipe_gerakan"),
                    'stock_adjustments.kode_penyesuaian as referensi',
                    DB::raw("CASE WHEN stock_adjustment_items.tipe = 'IN' THEN stock_adjustment_items.jumlah ELSE 0 END as jumlah_masuk"),
                    DB::raw("CASE WHEN stock_adjustment_items.tipe = 'OUT' THEN stock_adjustment_items.jumlah ELSE 0 END as jumlah_keluar"),
                    'stock_adjustment_items.alasan as keterangan'
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
            $pdf = Pdf::loadView('content.laporan.pdf.export-inventaris', $data);
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
        $query = Purchase::with(['supplier', 'user'])
            ->latest('tanggal_pembelian'); // ->select() tidak diperlukan, Eloquent akan memilih semua kolom secara default.

        // Terapkan filter
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

        $totals = $totalQuery->reorder()->selectRaw('
            SUM(total_akhir) as grand_total,
            COUNT(id) as total_transactions
        ')->first();

        // Hitung total produk yang diterima dari transaksi yang sudah difilter
        $totals->total_products_received = $totalItemsQuery
            ->where('status_barang', 'Diterima') // Hanya hitung yang statusnya diterima
            ->join('purchase_items', 'purchases.id', '=', 'purchase_items.purchase_id')
            ->sum('purchase_items.qty');

        // Hitung total hutang (opsional, jika masih ingin digunakan di tempat lain)
        $totals->total_due = $totalQuery->sum(DB::raw('total_akhir - jumlah_dibayar'));

        // Lakukan paginasi
        $purchases = $query->paginate(50)->withQueryString();

        return view('content.laporan.laporan-pembelian', [
            'title' => 'Laporan Purchase',
            'purchases' => $purchases,
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'statusPembayaranOptions' => ['Lunas', 'Belum Lunas', 'Dibatalkan'],
            'statusBarangOptions' => ['Diterima', 'Belum Diterima', 'Dibatalkan'],
            'totals' => $totals,
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
        $profilToko = Stores::first();
        $type = $request->query('type', 'xlsx');

        // Gunakan query yang sama dengan method pembelian() untuk konsistensi filter
        $query = Purchase::with(['supplier', 'user'])
            ->latest('tanggal_pembelian');

        // Terapkan filter
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
            $pdf = Pdf::loadView('content.laporan.pdf.export-pembelian', $data);
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
        $query = Sale::with(['customer', 'user'])
            ->latest('tanggal_penjualan'); // ->select() tidak diperlukan, Eloquent akan memilih semua kolom secara default.

        // Terapkan filter
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

        $totals = $totalQuery->reorder()->selectRaw('
            SUM(total_akhir) as grand_total,
            COUNT(id) as total_transactions
        ')->first();

        // Hitung total item terjual dari transaksi yang sudah difilter
        $totals->total_products_sold = $totalItemsQuery->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')->sum('sale_items.jumlah');

        // Lakukan paginasi
        $sales = $query->paginate(50)->withQueryString();

        return view('content.laporan.laporan-penjualan', [
            'title' => 'Laporan Sale',
            'sales' => $sales,
            'customers' => Customer::orderBy('name')->get(['id', 'name']),
            'statusPembayaranOptions' => ['Lunas', 'Belum Lunas', 'Dibatalkan'],
            'totals' => $totals,
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
        $profilToko = Stores::first();
        $type = $request->query('type', 'xlsx');

        // Gunakan query yang sama dengan method penjualan() untuk konsistensi filter
        $query = Sale::with(['customer', 'user'])
            ->latest('tanggal_penjualan');

        // Terapkan filter
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
            $pdf = Pdf::loadView('content.laporan.pdf.export-penjualan', $data);
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

        // 2. Hitung Total Pendapatan dari Sale (yang tidak dibatalkan)
        $totalRevenue = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        // Tambahan: Hitung Total Pendapatan Lain-lain dari tabel Income
        $totalOtherIncome = Income::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        // 3. Hitung Harga Pokok Sale (HPP / COGS)
        // HPP = Jumlah barang terjual * harga beli produk
        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->sum(DB::raw('sale_items.jumlah * products.harga_beli'));

        // 4. Hitung Laba Kotor (Pendapatan - HPP)
        $grossProfit = $totalRevenue - $cogs;

        // 5. Hitung Total Beban Operasional dari tabel expense
        $totalExpenses = Expense::whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');

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
                ->sum('total_akhir');

            // HPP bulan ini
            $monthlyCogs = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->whereBetween('sales.tanggal_penjualan', [$monthStart, $monthEnd])
                ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
                ->sum(DB::raw('sale_items.jumlah * products.harga_beli'));

            // Income lain-lain bulan ini
            $monthlyOtherIncome = Income::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('jumlah');

            // Beban bulan ini
            $monthlyExpenses = Expense::whereBetween('tanggal', [$monthStart, $monthEnd])->sum('jumlah');

            // Laba bersih bulan ini
            $monthlyGrossProfit = $monthlyRevenue - $monthlyCogs;
            $monthlyNetProfit = $monthlyGrossProfit + $monthlyOtherIncome - $monthlyExpenses;

            // Tambahkan ke array untuk dikirim ke view
            $chartLabels[] = $date->isoFormat('MMMM Y');
            $chartNetProfits[] = $monthlyNetProfit;
        }

        return view('content.laporan.laporan-laba-rugi', [
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
        $profilToko = Stores::first();
        // 1. Atur rentang tanggal dari request
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // 2. Hitung Total Pendapatan
        $totalRevenue = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        // Tambahan: Hitung Total Pendapatan Lain-lain
        $totalOtherIncome = Income::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        // 3. Hitung HPP
        $cogs = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->sum(DB::raw('sale_items.jumlah * products.harga_beli'));

        // 4. Hitung Laba Kotor
        $grossProfit = $totalRevenue - $cogs;

        // 5. Hitung Total Beban
        $totalExpenses = Expense::whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');

        // 6. Hitung Laba Bersih
        $netProfit = $grossProfit + $totalOtherIncome - $totalExpenses;

        // 7. Siapkan data untuk view PDF
        $pdf = Pdf::loadView('content.laporan.pdf.export-laba', [
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
