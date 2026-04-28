<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\Purchase;
use Modules\Inventory\Models\Supplier;
use Modules\POS\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil request untuk filter tanggal
        $request = request();

        // 1. Atur rentang tanggal, defaultnya bulan ini
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // --- DATA UNTUK STATS CARDS ---

        // --- Perbandingan dengan Periode Sebelumnya ---
        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::parse($endDate);
        $daysDifference = $endCarbon->diffInDays($startCarbon);

        // Tentukan periode sebelumnya dengan duration yang sama
        $previousStartDate = $startCarbon->copy()->subDays($daysDifference + 1);
        $previousEndDate = $endCarbon->copy()->subDays($daysDifference + 1);

        // 1. Pendapatan Periode Ini vs Periode Sebelumnya
        $pendapatanPeriodeIni = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])->where('status_pembayaran', '!=', 'Dibatalkan')->sum('total_akhir');
        $pendapatanPeriodeLalu = Sale::whereBetween('tanggal_penjualan', [$previousStartDate, $previousEndDate])->where('status_pembayaran', '!=', 'Dibatalkan')->sum('total_akhir');

        // 2. Transaksi Periode Ini vs Periode Sebelumnya
        $transaksiPeriodeIni = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])->where('status_pembayaran', '!=', 'Dibatalkan')->count();
        $transaksiPeriodeLalu = Sale::whereBetween('tanggal_penjualan', [$previousStartDate, $previousEndDate])->where('status_pembayaran', '!=', 'Dibatalkan')->count();

        // 3. Hitung persentase perubahan (untuk pendapatan dan transaksi)
        $persentasePendapatan = 0;
        if ($pendapatanPeriodeLalu > 0) {
            $persentasePendapatan = (($pendapatanPeriodeIni - $pendapatanPeriodeLalu) / $pendapatanPeriodeLalu) * 100;
        } elseif ($pendapatanPeriodeIni > 0) {
            $persentasePendapatan = 100; // Jika periode lalu 0 dan periode ini ada penjualan
        }

        $persentaseTransaksi = 0;
        if ($transaksiPeriodeLalu > 0) {
            $persentaseTransaksi = (($transaksiPeriodeIni - $transaksiPeriodeLalu) / $transaksiPeriodeLalu) * 100;
        } elseif ($transaksiPeriodeIni > 0) {
            $persentaseTransaksi = 100;
        }

        // --- DATA UNTUK STATS CARDS ---

       /// Ambil ID Toko user yang sedang login
        $currentStoreId = Auth::user()->profile->store_id;

        // 1. Siapkan Query Dasar
        // withSum berfungsi agar variabel 'total_stok' otomatis tersedia saat di-looping di Blade
        $lowStockQuery = Product::withSum(['stocks as total_stok' => function($query) use ($currentStoreId) {
                $query->where('store_id', $currentStoreId);
            }], 'qty')
            // whereRaw berfungsi untuk memfilter data secara aman tanpa melanggar aturan MySQL
            ->whereRaw('
                COALESCE((
                    SELECT SUM(qty) 
                    FROM product_stocks 
                    WHERE product_stocks.product_id = products.id 
                    AND product_stocks.store_id = ?
                ), 0) <= products.stok_minimum
            ', [$currentStoreId]);

        // 2. Hitung jumlah total produk yang stoknya rendah
        // Kabar baik! Karena kita pakai whereRaw, kita bisa kembali menggunakan ->count() biasa dengan sangat aman
        $stokRendahCount = (clone $lowStockQuery)->count();

        // 3. Ambil 5 produk dengan stok paling menipis untuk ditampilkan di widget
        $produkStockRendah = clone($lowStockQuery)
            ->orderBy('total_stok', 'asc')
            ->limit(5)
            ->get();

        // 5. Total Sale & Purchase Berdasarkan Periode
        $totalSalePeriode = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        $totalPurchasePeriode = Purchase::whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        // Total Expense Berdasarkan Periode
        $totalExpensePeriode = Expense::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        // Hitung Harga Pokok Sale (HPP / COGS) Berdasarkan Periode
        $cogsPeriode = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->sum(DB::raw('sale_items.jumlah * products.harga_beli'));

        // Hitung Laba Bersih Berdasarkan Periode (Pendapatan - HPP - Expense)
        $labaBersihPeriode = $totalSalePeriode - $cogsPeriode - $totalExpensePeriode;


        // Total Retur Sale & Purchase Berdasarkan Periode
        $totalReturSalePeriode = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', 'Dibatalkan')
            ->sum('total_akhir');

        $totalReturPurchasePeriode = Purchase::whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->where('status_pembayaran', 'Dibatalkan')
            ->sum('total_akhir');

        $totalCustomer = Customer::count();
        $totalSupplier = Supplier::count();
        $totalOrder = Sale::count();
        $totalPurchase = Purchase::count();



        // --- DATA UNTUK GRAFIK PENJUALAN (30 HARI TERAKHIR) ---
        $salesData = Sale::select(
            DB::raw('DATE(tanggal_penjualan) as tanggal'),
            DB::raw('SUM(total_akhir) as total')
        )
            ->where('tanggal_penjualan', '>=', Carbon::now()->subDays(30))
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $salesChartLabels = $salesData->pluck('tanggal')->map(function ($date) {
            return Carbon::parse($date)->format('d M');
        });
        $salesChartData = $salesData->pluck('total');


        // --- DATA UNTUK PRODUK TERLARIS (BERDASARKAN PERIODE) ---
        // 1. Dapatkan produk terlaris periode ini beserta jumlah terjual dan harga jualnya
        $currentMonthSales = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                'products.id as product_id',
                'products.name_product',
                'products.img_produk',
                'products.harga_jual',
                DB::raw('SUM(sale_items.jumlah) as total_terjual_current_month')
            )
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->groupBy('products.id', 'products.name_product', 'products.img_produk', 'products.harga_jual')
            ->orderBy('total_terjual_current_month', 'desc')
            ->limit(5)
            ->get();

        // 2. Dapatkan penjualan bulan sebelumnya untuk produk-produk terlaris ini
        $previousMonthSales = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                'sale_items.product_id',
                DB::raw('SUM(sale_items.jumlah) as total_terjual_previous_month')
            )
            ->whereIn('sale_items.product_id', $currentMonthSales->pluck('product_id'))
            ->whereBetween('sales.tanggal_penjualan', [$previousStartDate, $previousEndDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->groupBy('sale_items.product_id')
            ->get()
            ->keyBy('product_id');

        // 3. Gabungkan data dan hitung persentase kenaikan
        $produkTerlaris = $currentMonthSales->map(function ($product) use ($previousMonthSales) {
            $previousSales = $previousMonthSales->get($product->product_id);
            $totalTerjualPreviousMonth = $previousSales ? $previousSales->total_terjual_previous_month : 0;

            $percentageIncrease = 0;
            if ($totalTerjualPreviousMonth > 0) {
                $percentageIncrease = (($product->total_terjual_current_month - $totalTerjualPreviousMonth) / $totalTerjualPreviousMonth) * 100;
            } elseif ($product->total_terjual_current_month > 0) {
                $percentageIncrease = 100; // Jika bulan sebelumnya 0 dan bulan ini ada penjualan
            }

            $product->percentage_increase = $percentageIncrease;
            $product->total_terjual = $product->total_terjual_current_month; // Sesuaikan name variabel untuk blade
            return $product;
        });

        // --- DATA UNTUK PELANGGAN TERBAIK (BERDASARKAN PERIODE) ---
        $pelangganTerbaik = Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'customers.name',
                DB::raw('COUNT(sales.id) as total_orders'),
                DB::raw('SUM(sales.total_akhir) as total_spent')
            )
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->whereNotNull('sales.customer_id') // Pastikan pelanggan ada
            ->where('customers.name', '!=', 'Customer Umum') // Abaikan pelanggan umum
            ->groupBy('customers.id', 'customers.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        $recentSales = Sale::with('customer')
            ->latest('tanggal_penjualan')
            ->take(11)
            ->get();

        $recentPurchases = Purchase::with('pemasok')
            ->latest('tanggal_pembelian')
            ->take(11)
            ->get();

        // --- DATA UNTUK GRAFIK KATEGORI TERLARIS (BERDASARKAN PERIODE) ---
        $categorySalesData = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                'categories.name as category_name',
                DB::raw('SUM(sale_items.jumlah) as total_sold')
            )
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->groupBy('categories.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5) // Ambil 5 kategori teratas
            ->get();

        $categoryChartLabels = $categorySalesData->pluck('category_name');
        $categoryChartData = $categorySalesData->pluck('total_sold');


        return view('content.dashboard.index', [
            'title' => 'Dashboard',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pendapatanPeriodeIni' => $pendapatanPeriodeIni,
            'persentasePendapatan' => $persentasePendapatan,
            'transaksiPeriodeIni' => $transaksiPeriodeIni,
            'persentaseTransaksi' => $persentaseTransaksi,
            'totalSalePeriode' => $totalSalePeriode,
            'totalPurchasePeriode' => $totalPurchasePeriode,
            'totalExpensePeriode' => $totalExpensePeriode,
            'labaBersihPeriode' => $labaBersihPeriode,
            'totalReturSalePeriode' => $totalReturSalePeriode,
            'totalReturPurchasePeriode' => $totalReturPurchasePeriode,
            'stokRendahCount' => $stokRendahCount,
            'salesChartLabels' => $salesChartLabels,
            'salesChartData' => $salesChartData,
            'produkTerlaris' => $produkTerlaris,
            'produkStockRendah' => $produkStockRendah,
            'pelangganTerbaik' => $pelangganTerbaik,
            'recentSales' => $recentSales,
            'recentPurchases' => $recentPurchases,
            'totalCustomer' => $totalCustomer,
            'totalSupplier' => $totalSupplier,
            'totalOrder' => $totalOrder,
            'totalPurchase' => $totalPurchase,
            'categoryChartLabels' => $categoryChartLabels,
            'categoryChartData' => $categoryChartData,
        ]);
    }
}
