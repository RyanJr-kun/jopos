<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use App\Models\Penjualan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class KeuanganController extends Controller
{
    /**
     * Menampilkan halaman utama administrasi keuangan.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // 1. Atur rentang tanggal default (bulan ini) atau ambil dari input filter
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // 2. Ambil data ringkasan dalam rentang tanggal yang ditentukan
        $totalPemasukan = Pemasukan::whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');
        $totalPengeluaran = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');
        $labaRugi = $totalPemasukan - $totalPengeluaran;
        $totalTransaksi = Pemasukan::whereBetween('tanggal', [$startDate, $endDate])->count()
            + Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])->count();

        // 3. Ambil transaksi terbaru (gabungan pemasukan & pengeluaran)
        $pemasukans = Pemasukan::with('kategori_transaksi')
            ->select('id', 'tanggal', 'keterangan', 'jumlah', 'kategori_transaksi_id', DB::raw("'pemasukan' as type"));

        $pengeluarans = Pengeluaran::with('kategori_transaksi')
            ->select('id', 'tanggal', 'keterangan', 'jumlah', 'kategori_transaksi_id', DB::raw("'pengeluaran' as type"));

        // Gabungkan, urutkan, dan batasi hasilnya
        $recentTransactions = $pemasukans->union($pengeluarans)->latest('tanggal')
            ->limit(10)
            ->get();

        // 4. Siapkan data untuk grafik
        $pemasukanPerHari = Pemasukan::whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(tanggal) as date'),
                DB::raw('SUM(jumlah) as total')
            ])
            ->pluck('total', 'date');

        $pengeluaranPerHari = Pengeluaran::whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(tanggal) as date'),
                DB::raw('SUM(jumlah) as total')
            ])
            ->pluck('total', 'date');

        $period = CarbonPeriod::create($startDate, $endDate);
        $chartLabels = collect($period)->map(fn($date) => $date->isoFormat('D MMM'));
        $pemasukanData = collect($period)->map(fn($date) => $pemasukanPerHari[$date->format('Y-m-d')] ?? 0);
        $pengeluaranData = collect($period)->map(fn($date) => $pengeluaranPerHari[$date->format('Y-m-d')] ?? 0);

        // 5. Ambil invoice penjualan terbaru
        $recentInvoices = Penjualan::with('pelanggan')
            ->latest()
            ->limit(5)
            ->get();

        // 6. Kirim semua data ke view
        return view('dashboard.keuangan.index', [
            'title' => 'Administrasi Keuangan',
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'labaRugi' => $labaRugi,
            'totalTransaksi' => $totalTransaksi,
            'recentTransactions' => $recentTransactions,
            'recentInvoices' => $recentInvoices,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'chartData' => [
                'labels' => $chartLabels,
                'pemasukan' => $pemasukanData,
                'pengeluaran' => $pengeluaranData,
            ],
        ]);
    }
}
