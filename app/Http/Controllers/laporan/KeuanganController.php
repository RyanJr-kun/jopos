<?php

namespace App\Http\Controllers\laporan;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Sale;
use App\Models\Expense;
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
        $totalIncome = Income::whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');
        $totalExpense = Expense::whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah');
        $labaRugi = $totalIncome - $totalExpense;
        $totalTransaksi = Income::whereBetween('tanggal', [$startDate, $endDate])->count()
            + Expense::whereBetween('tanggal', [$startDate, $endDate])->count();

        // 3. Ambil transaksi terbaru (gabungan income & expense)
        $incomes = Income::with('transaction_category')
            ->select('id', 'tanggal', 'keterangan', 'jumlah', 'transaction_category_id', DB::raw("'income' as type"));

        $expenses = Expense::with('transaction_category')
            ->select('id', 'tanggal', 'keterangan', 'jumlah', 'transaction_category_id', DB::raw("'expense' as type"));

        // Gabungkan, urutkan, dan batasi hasilnya
        $recentTransactions = $incomes->union($expenses)->latest('tanggal')
            ->limit(10)
            ->get();

        // 4. Siapkan data untuk grafik
        $incomePerHari = Income::whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(tanggal) as date'),
                DB::raw('SUM(jumlah) as total')
            ])
            ->pluck('total', 'date');

        $expensePerHari = Expense::whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get([
                DB::raw('DATE(tanggal) as date'),
                DB::raw('SUM(jumlah) as total')
            ])
            ->pluck('total', 'date');

        $period = CarbonPeriod::create($startDate, $endDate);
        $chartLabels = collect($period)->map(fn($date) => $date->isoFormat('D MMM'));
        $incomeData = collect($period)->map(fn($date) => $incomePerHari[$date->format('Y-m-d')] ?? 0);
        $expenseData = collect($period)->map(fn($date) => $expensePerHari[$date->format('Y-m-d')] ?? 0);

        // 5. Ambil invoice penjualan terbaru
        $recentInvoices = Sale::with('pelanggan')
            ->latest()
            ->limit(5)
            ->get();

        // 6. Kirim semua data ke view
        return view('content.keuangan.index', [
            'title' => 'Administrasi Keuangan',
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'labaRugi' => $labaRugi,
            'totalTransaksi' => $totalTransaksi,
            'recentTransactions' => $recentTransactions,
            'recentInvoices' => $recentInvoices,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'chartData' => [
                'labels' => $chartLabels,
                'income' => $incomeData,
                'expense' => $expenseData,
            ],
        ]);
    }
}
