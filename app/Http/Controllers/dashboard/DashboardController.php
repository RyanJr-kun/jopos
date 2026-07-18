<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\CashFlow;
use App\Models\Customer;
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
        $request = request();

        // Rentang tanggal, default bulan ini
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Periode sebelumnya untuk perbandingan
        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::parse($endDate);
        $daysDifference = $endCarbon->diffInDays($startCarbon);

        $previousStartDate = $startCarbon->copy()->subDays($daysDifference + 1);
        $previousEndDate = $endCarbon->copy()->subDays($daysDifference + 1);

        // --- PENDAPATAN ---
        $pendapatanPeriodeIni = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        $pendapatanPeriodeLalu = Sale::whereBetween('tanggal_penjualan', [$previousStartDate, $previousEndDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        $persentasePendapatan = 0;
        if ($pendapatanPeriodeLalu > 0) {
            $persentasePendapatan = (($pendapatanPeriodeIni - $pendapatanPeriodeLalu) / $pendapatanPeriodeLalu) * 100;
        } elseif ($pendapatanPeriodeIni > 0) {
            $persentasePendapatan = 100;
        }

        // --- TRANSAKSI ---
        $transaksiPeriodeIni = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->count();

        $transaksiPeriodeLalu = Sale::whereBetween('tanggal_penjualan', [$previousStartDate, $previousEndDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->count();

        $persentaseTransaksi = 0;
        if ($transaksiPeriodeLalu > 0) {
            $persentaseTransaksi = (($transaksiPeriodeIni - $transaksiPeriodeLalu) / $transaksiPeriodeLalu) * 100;
        } elseif ($transaksiPeriodeIni > 0) {
            $persentaseTransaksi = 100;
        }

        // --- STOK RENDAH ---
        $currentStoreId = Auth::user()->employee->store_id;

        $lowStockQuery = Product::withSum(['stocks as total_stok' => function ($query) use ($currentStoreId) {
            $query->where('store_id', $currentStoreId);
        }], 'qty')
            ->whereRaw('
                COALESCE((
                    SELECT SUM(qty) 
                    FROM product_stocks 
                    WHERE product_stocks.product_id = products.id 
                    AND product_stocks.store_id = ?
                ), 0) <= products.stok_minimum
            ', [$currentStoreId]);

        $stokRendahCount = (clone $lowStockQuery)->count();

        $produkStockRendah = (clone $lowStockQuery)
            ->orderBy('total_stok', 'asc')
            ->limit(5)
            ->get();

        // --- TOTAL SALE & PURCHASE (PERIODE) ---
        $totalSalePeriode = Sale::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        $totalPurchasePeriode = Purchase::whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        // --- EXPENSE (dari CashFlow) ---
        $totalExpensePeriode = CashFlow::expense()
            ->aktif()
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('nominal');

        // --- LABA BERSIH (Pendapatan - HPP - Expense) ---
        $cogsPeriode = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->sum(DB::raw('sale_items.jumlah * products.harga_beli'));

        $labaBersihPeriode = $totalSalePeriode - $cogsPeriode - $totalExpensePeriode;

        // --- GRAFIK PENJUALAN (30 HARI TERAKHIR) ---
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

        // --- PRODUK TERLARIS (BERDASARKAN PERIODE) ---
        $currentMonthSales = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select(
                'products.id as product_id',
                'products.name_product',
                'products.harga_jual',
                DB::raw('SUM(sale_items.jumlah) as total_terjual_current_month')
            )
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->groupBy('products.id', 'products.name_product', 'products.harga_jual')
            ->orderBy('total_terjual_current_month', 'desc')
            ->limit(5)
            ->get();

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

        $produkTerlaris = $currentMonthSales->map(function ($product) use ($previousMonthSales) {
            $previousSales = $previousMonthSales->get($product->product_id);
            $totalTerjualPreviousMonth = $previousSales ? $previousSales->total_terjual_previous_month : 0;

            $percentageIncrease = 0;
            if ($totalTerjualPreviousMonth > 0) {
                $percentageIncrease = (($product->total_terjual_current_month - $totalTerjualPreviousMonth) / $totalTerjualPreviousMonth) * 100;
            } elseif ($product->total_terjual_current_month > 0) {
                $percentageIncrease = 100;
            }

            $productModel = Product::find($product->product_id);
            $product->image_url = $productModel ? $productModel->image_url : asset('assets/img/produk.png');
            $product->percentage_increase = $percentageIncrease;
            $product->total_terjual = $product->total_terjual_current_month;
            return $product;
        });

        // --- PELANGGAN TERBAIK ---
        $pelangganTerbaik = Sale::join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'customers.name',
                DB::raw('COUNT(sales.id) as total_orders'),
                DB::raw('SUM(sales.total_akhir) as total_spent')
            )
            ->whereBetween('sales.tanggal_penjualan', [$startDate, $endDate])
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->whereNotNull('sales.customer_id')
            ->where('customers.name', '!=', 'Customer Umum')
            ->groupBy('customers.id', 'customers.name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        // --- AKTIVITAS TERAKHIR ---
        $recentSales = Sale::with('customer')
            ->latest('tanggal_penjualan')
            ->take(11)
            ->get();

        $recentPurchases = Purchase::with('supplier')
            ->latest('tanggal_pembelian')
            ->take(11)
            ->get();

        // --- KATEGORI TERLARIS (PIE CHART) ---
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
            ->limit(5)
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
            'stokRendahCount' => $stokRendahCount,
            'salesChartLabels' => $salesChartLabels,
            'salesChartData' => $salesChartData,
            'produkTerlaris' => $produkTerlaris,
            'produkStockRendah' => $produkStockRendah,
            'pelangganTerbaik' => $pelangganTerbaik,
            'recentSales' => $recentSales,
            'recentPurchases' => $recentPurchases,
            'categoryChartLabels' => $categoryChartLabels,
            'categoryChartData' => $categoryChartData,
        ]);
    }
}
