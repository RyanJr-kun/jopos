<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pemasok;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\Pengeluaran;
use App\Models\Penjualan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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

        // Tentukan periode sebelumnya dengan durasi yang sama
        $previousStartDate = $startCarbon->copy()->subDays($daysDifference + 1);
        $previousEndDate = $endCarbon->copy()->subDays($daysDifference + 1);

        // 1. Pendapatan Periode Ini vs Periode Sebelumnya
        $pendapatanPeriodeIni = Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])->where('status_pembayaran', '!=', 'Dibatalkan')->sum('total_akhir');
        $pendapatanPeriodeLalu = Penjualan::whereBetween('tanggal_penjualan', [$previousStartDate, $previousEndDate])->where('status_pembayaran', '!=', 'Dibatalkan')->sum('total_akhir');

        // 2. Transaksi Periode Ini vs Periode Sebelumnya
        $transaksiPeriodeIni = Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])->where('status_pembayaran', '!=', 'Dibatalkan')->count();
        $transaksiPeriodeLalu = Penjualan::whereBetween('tanggal_penjualan', [$previousStartDate, $previousEndDate])->where('status_pembayaran', '!=', 'Dibatalkan')->count();

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

        // 4. Produk dengan Stok Rendah (berdasarkan stok minimum per produk)
        $lowStockQuery = Produk::whereColumn('qty', '<=', 'stok_minimum');
        $stokRendahCount = (clone $lowStockQuery)->count();
        $produkStokRendah = $lowStockQuery->orderBy('qty', 'asc')->limit(5)->get();

        // 5. Total Penjualan & Pembelian Berdasarkan Periode
        $totalPenjualanPeriode = Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        $totalPembelianPeriode = Pembelian::whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->where('status_pembayaran', '!=', 'Dibatalkan')
            ->sum('total_akhir');

        // Total Pengeluaran Berdasarkan Periode
        $totalPengeluaranPeriode = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah');

        // Hitung Harga Pokok Penjualan (HPP / COGS) Berdasarkan Periode
        $cogsPeriode = DB::table('item_penjualans')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->join('produks', 'item_penjualans.produk_id', '=', 'produks.id')
            ->whereBetween('penjualans.tanggal_penjualan', [$startDate, $endDate])
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->sum(DB::raw('item_penjualans.jumlah * produks.harga_beli'));

        // Hitung Laba Bersih Berdasarkan Periode (Pendapatan - HPP - Pengeluaran)
        $labaBersihPeriode = $totalPenjualanPeriode - $cogsPeriode - $totalPengeluaranPeriode;


        // Total Retur Penjualan & Pembelian Berdasarkan Periode
        $totalReturPenjualanPeriode = Penjualan::whereBetween('tanggal_penjualan', [$startDate, $endDate])
            ->where('status_pembayaran', 'Dibatalkan')
            ->sum('total_akhir');

        $totalReturPembelianPeriode = Pembelian::whereBetween('tanggal_pembelian', [$startDate, $endDate])
            ->where('status_pembayaran', 'Dibatalkan')
            ->sum('total_akhir');

        $totalPelanggan = Pelanggan::count();
        $totalPemasok = Pemasok::count();
        $totalOrder = Penjualan::count();
        $totalPembelian = Pembelian::count();



        // --- DATA UNTUK GRAFIK PENJUALAN (30 HARI TERAKHIR) ---
        $salesData = Penjualan::select(
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
        $currentMonthSales = DB::table('item_penjualans')
            ->join('produks', 'item_penjualans.produk_id', '=', 'produks.id')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->select(
                'produks.id as produk_id',
                'produks.nama_produk',
                'produks.img_produk',
                'produks.harga_jual',
                DB::raw('SUM(item_penjualans.jumlah) as total_terjual_current_month')
            )
            ->whereBetween('penjualans.tanggal_penjualan', [$startDate, $endDate])
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->groupBy('produks.id', 'produks.nama_produk', 'produks.img_produk', 'produks.harga_jual')
            ->orderBy('total_terjual_current_month', 'desc')
            ->limit(5)
            ->get();

        // 2. Dapatkan penjualan bulan sebelumnya untuk produk-produk terlaris ini
        $previousMonthSales = DB::table('item_penjualans')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->select(
                'item_penjualans.produk_id',
                DB::raw('SUM(item_penjualans.jumlah) as total_terjual_previous_month')
            )
            ->whereIn('item_penjualans.produk_id', $currentMonthSales->pluck('produk_id'))
            ->whereBetween('penjualans.tanggal_penjualan', [$previousStartDate, $previousEndDate])
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->groupBy('item_penjualans.produk_id')
            ->get()
            ->keyBy('produk_id');

        // 3. Gabungkan data dan hitung persentase kenaikan
        $produkTerlaris = $currentMonthSales->map(function ($product) use ($previousMonthSales) {
            $previousSales = $previousMonthSales->get($product->produk_id);
            $totalTerjualPreviousMonth = $previousSales ? $previousSales->total_terjual_previous_month : 0;

            $percentageIncrease = 0;
            if ($totalTerjualPreviousMonth > 0) {
                $percentageIncrease = (($product->total_terjual_current_month - $totalTerjualPreviousMonth) / $totalTerjualPreviousMonth) * 100;
            } elseif ($product->total_terjual_current_month > 0) {
                $percentageIncrease = 100; // Jika bulan sebelumnya 0 dan bulan ini ada penjualan
            }

            $product->percentage_increase = $percentageIncrease;
            $product->total_terjual = $product->total_terjual_current_month; // Sesuaikan nama variabel untuk blade
            return $product;
        });

        // --- DATA UNTUK PELANGGAN TERBAIK (BERDASARKAN PERIODE) ---
        $pelangganTerbaik = Penjualan::join('pelanggans', 'penjualans.pelanggan_id', '=', 'pelanggans.id')
            ->select(
                'pelanggans.nama',
                DB::raw('COUNT(penjualans.id) as total_orders'),
                DB::raw('SUM(penjualans.total_akhir) as total_spent')
            )
            ->whereBetween('penjualans.tanggal_penjualan', [$startDate, $endDate])
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->whereNotNull('penjualans.pelanggan_id') // Pastikan pelanggan ada
            ->where('pelanggans.nama', '!=', 'Pelanggan Umum') // Abaikan pelanggan umum
            ->groupBy('pelanggans.id', 'pelanggans.nama')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        $recentSales = Penjualan::with('pelanggan')
            ->latest('tanggal_penjualan')
            ->take(11)
            ->get();

        $recentPurchases = Pembelian::with('pemasok')
            ->latest('tanggal_pembelian')
            ->take(11)
            ->get();

        // --- DATA UNTUK GRAFIK KATEGORI TERLARIS (BERDASARKAN PERIODE) ---
        $categorySalesData = DB::table('item_penjualans')
            ->join('produks', 'item_penjualans.produk_id', '=', 'produks.id')
            ->join('kategori_produks', 'produks.kategori_produk_id', '=', 'kategori_produks.id')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->select(
                'kategori_produks.nama as category_name',
                DB::raw('SUM(item_penjualans.jumlah) as total_sold')
            )
            ->whereBetween('penjualans.tanggal_penjualan', [$startDate, $endDate])
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->groupBy('kategori_produks.nama')
            ->orderBy('total_sold', 'desc')
            ->limit(5) // Ambil 5 kategori teratas
            ->get();

        $categoryChartLabels = $categorySalesData->pluck('category_name');
        $categoryChartData = $categorySalesData->pluck('total_sold');


        return view('dashboard.index', [
            'title' => 'Dashboard',
            'startDate' => $startDate,
            'endDate' => $endDate,
            'pendapatanPeriodeIni' => $pendapatanPeriodeIni,
            'persentasePendapatan' => $persentasePendapatan,
            'transaksiPeriodeIni' => $transaksiPeriodeIni,
            'persentaseTransaksi' => $persentaseTransaksi,
            'totalPenjualanPeriode' => $totalPenjualanPeriode,
            'totalPembelianPeriode' => $totalPembelianPeriode,
            'totalPengeluaranPeriode' => $totalPengeluaranPeriode,
            'labaBersihPeriode' => $labaBersihPeriode,
            'totalReturPenjualanPeriode' => $totalReturPenjualanPeriode,
            'totalReturPembelianPeriode' => $totalReturPembelianPeriode,
            'stokRendahCount' => $stokRendahCount,
            'salesChartLabels' => $salesChartLabels,
            'salesChartData' => $salesChartData,
            'produkTerlaris' => $produkTerlaris,
            'produkStokRendah' => $produkStokRendah,
            'pelangganTerbaik' => $pelangganTerbaik,
            'recentSales' => $recentSales,
            'recentPurchases' => $recentPurchases,
            'totalPelanggan' => $totalPelanggan,
            'totalPemasok' => $totalPemasok,
            'totalOrder' => $totalOrder,
            'totalPembelian' => $totalPembelian,
            'categoryChartLabels' => $categoryChartLabels,
            'categoryChartData' => $categoryChartData,
        ]);
    }
}
