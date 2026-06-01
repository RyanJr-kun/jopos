<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Mutasi;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\POS\Models\Sale;

class KeuanganController extends Controller
{
    // =========================================================================
    // DASHBOARD UTAMA
    // =========================================================================

    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->input('end_date',   Carbon::now()->endOfMonth()->format('Y-m-d'));

        // --- Filter metode pembayaran ---
        $metodePembayaran = $request->input('metode'); // store | tunai | transfer | qris
        $bankId           = $request->input('bank_id');

        // --- Ringkasan (Income & Expense) ---
        $incomeQuery  = Income::whereBetween('tanggal', [$startDate, $endDate]);
        $expenseQuery = Expense::whereBetween('tanggal', [$startDate, $endDate]);

        if ($metodePembayaran) {
            $incomeQuery->where('metode_pembayaran', $metodePembayaran);
            $expenseQuery->where('metode_pembayaran', $metodePembayaran);
        }
        if ($bankId) {
            $incomeQuery->where('bank_id', $bankId);
            $expenseQuery->where('bank_id', $bankId);
        }

        $totalIncome  = (clone $incomeQuery)->sum('jumlah');
        $totalExpense = (clone $expenseQuery)->sum('jumlah');
        $labaRugi     = $totalIncome - $totalExpense;
        $totalTransaksi = (clone $incomeQuery)->count() + (clone $expenseQuery)->count();

        // --- Breakdown metode pembayaran (untuk card detail mutasi) ---
        $breakdownIncome = Income::query()->whereBetween('tanggal', [$startDate, $endDate])
            ->select('metode_pembayaran', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as jumlah_transaksi'))
            ->groupBy('metode_pembayaran')
            ->get()
            ->keyBy('metode_pembayaran');

        $breakdownExpense = Expense::query()->whereBetween('tanggal', [$startDate, $endDate])
            ->select('metode_pembayaran', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(*) as jumlah_transaksi'))
            ->groupBy('metode_pembayaran')
            ->get()
            ->keyBy('metode_pembayaran');

        // --- Saldo per rekening bank ---
        $saldoPerBank = Bank::query()->where('is_active', true)
            ->withSum(['incomes as total_masuk' => fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate])], 'jumlah')
            ->withSum(['expenses as total_keluar' => fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate])], 'jumlah')
            ->get()
            ->map(fn($bank) => [
                'bank'         => $bank,
                'total_masuk'  => $bank->total_masuk ?? 0,
                'total_keluar' => $bank->total_keluar ?? 0,
                'saldo'        => ($bank->total_masuk ?? 0) - ($bank->total_keluar ?? 0),
            ]);

        // --- Transaksi terbaru (gabungan income + expense) ---
        $incomes  = Income::with(['transaction_category', 'bank'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('id', 'tanggal', 'keterangan', 'jumlah', 'transaction_category_id', 'metode_pembayaran', 'bank_id', DB::raw("'income' as type"));

        $expenses = Expense::with(['transaction_category', 'bank'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->select('id', 'tanggal', 'keterangan', 'jumlah', 'transaction_category_id', 'metode_pembayaran', 'bank_id', DB::raw("'expense' as type"));

        if ($metodePembayaran) {
            $incomes->where('metode_pembayaran', $metodePembayaran);
            $expenses->where('metode_pembayaran', $metodePembayaran);
        }
        if ($bankId) {
            $incomes->where('bank_id', $bankId);
            $expenses->where('bank_id', $bankId);
        }

        $recentTransactions = $incomes->union($expenses)
            ->latest('tanggal')
            ->limit(15)
            ->get();

        // --- Data grafik (harian) ---
        $incomePerHari = Income::whereBetween('tanggal', [$startDate, $endDate])
            ->when($metodePembayaran, fn($q) => $q->where('metode_pembayaran', $metodePembayaran))
            ->when($bankId, fn($q) => $q->where('bank_id', $bankId))
            ->groupBy('date')->orderBy('date')
            ->get([DB::raw('DATE(tanggal) as date'), DB::raw('SUM(jumlah) as total')])
            ->pluck('total', 'date');

        $expensePerHari = Expense::whereBetween('tanggal', [$startDate, $endDate])
            ->when($metodePembayaran, fn($q) => $q->where('metode_pembayaran', $metodePembayaran))
            ->when($bankId, fn($q) => $q->where('bank_id', $bankId))
            ->groupBy('date')->orderBy('date')
            ->get([DB::raw('DATE(tanggal) as date'), DB::raw('SUM(jumlah) as total')])
            ->pluck('total', 'date');

        $period      = CarbonPeriod::create($startDate, $endDate);
        $chartLabels = collect($period)->map(fn($d) => $d->isoFormat('D MMM'));
        $incomeData  = collect($period)->map(fn($d) => (int)($incomePerHari[$d->format('Y-m-d')] ?? 0));
        $expenseData = collect($period)->map(fn($d) => (int)($expensePerHari[$d->format('Y-m-d')] ?? 0));

        // --- Daftar bank untuk filter & modal ---
        $banks = Bank::query()->where('is_active', true)->orderBy('nama_bank')->get();

        return view('content.finance.index', [
            'title'              => 'Dashboard Keuangan',
            'startDate'          => $startDate,
            'endDate'            => $endDate,
            'metodePembayaran'   => $metodePembayaran,
            'bankIdFilter'       => $bankId,
            'totalIncome'        => $totalIncome,
            'totalExpense'       => $totalExpense,
            'labaRugi'           => $labaRugi,
            'totalTransaksi'     => $totalTransaksi,
            'breakdownIncome'    => $breakdownIncome,
            'breakdownExpense'   => $breakdownExpense,
            'saldoPerBank'       => $saldoPerBank,
            'recentTransactions' => $recentTransactions,
            'banks'              => $banks,
            'chartData'          => [
                'labels'  => $chartLabels,
                'income'  => $incomeData,
                'expense' => $expenseData,
            ],
        ]);
    }

    // =========================================================================
    // BANK — CRUD (store & update dari KeuanganController)
    // =========================================================================

    /**
     * Simpan bank baru. Logo diunggah ke Cloudflare R2.
     */
    public function storeBank(Request $request)
    {
        $validated = $request->validate([
            'nama_bank'       => 'required|string|max:100',
            'nomor_rekening'  => 'required|string|max:50',
            'nama_pemilik'    => 'required|string|max:100',
            'logo_bank'       => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'is_active'       => 'boolean',
        ]);

        if ($request->hasFile('logo_bank')) {
            $file      = $request->file('logo_bank');
            $filename  = 'banks/' . Str::slug($validated['nama_bank']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['logo_bank'] = Storage::disk('r2')->putFileAs('', $file, $filename) ? $filename : null;
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Bank::create($validated);

        return back()->with('success', 'Bank berhasil ditambahkan.');
    }

    /**
     * Update data bank. Ganti logo jika ada file baru.
     */
    public function updateBank(Request $request, Bank $bank)
    {
        $validated = $request->validate([
            'nama_bank'       => 'required|string|max:100',
            'nomor_rekening'  => 'required|string|max:50',
            'nama_pemilik'    => 'required|string|max:100',
            'logo_bank'       => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'is_active'       => 'boolean',
        ]);

        if ($request->hasFile('logo_bank')) {
            // Hapus logo lama dari R2
            if ($bank->logo_bank && Storage::disk('r2')->exists($bank->logo_bank)) {
                Storage::disk('r2')->delete($bank->logo_bank);
            }

            $file      = $request->file('logo_bank');
            $filename  = 'banks/' . Str::slug($validated['nama_bank']) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $validated['logo_bank'] = Storage::disk('r2')->putFileAs('', $file, $filename) ? $filename : null;
        } else {
            unset($validated['logo_bank']); // Jangan timpa logo lama
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $bank->update($validated);

        return back()->with('success', 'Data bank berhasil diperbarui.');
    }

    /**
     * Hapus bank (soft: nonaktifkan, atau hard delete jika tidak ada transaksi).
     */
    public function destroyBank(Bank $bank)
    {
        $hasTransaksi = Income::query()->where('bank_id', $bank->id)->exists()
            || Expense::query()->where('bank_id', $bank->id)->exists();

        if ($hasTransaksi) {
            $bank->update(['is_active' => false]);
            return back()->with('info', 'Bank memiliki riwayat transaksi, dinonaktifkan saja.');
        }

        if ($bank->logo_bank && Storage::disk('r2')->exists($bank->logo_bank)) {
            Storage::disk('r2')->delete($bank->logo_bank);
        }

        $bank->delete();

        return back()->with('success', 'Bank berhasil dihapus.');
    }

    // =========================================================================
    // MUTASI — Uang masuk & keluar ke rekening / kasir
    // =========================================================================

    /**
     * Simpan mutasi (Income atau Expense tergantung tipe).
     *
     * Request params:
     *  - tipe            : 'masuk' | 'keluar'
     *  - tanggal         : date
     *  - jumlah          : numeric
     *  - keterangan      : string
     *  - metode_pembayaran: 'store' | 'tunai' | 'transfer' | 'qris'
     *  - bank_id         : int|null (wajib jika transfer atau qris)
     *  - transaction_category_id : int|null
     */
    public function storeMutasi(Request $request)
    {
        $validated = $request->validate([
            'tipe'                    => 'required|in:masuk,keluar',
            'tanggal'                 => 'required|date',
            'jumlah'                  => 'required|numeric|min:1',
            'keterangan'              => 'required|string|max:500',
            'metode_pembayaran'       => 'required|in:store,tunai,transfer,qris',
            'bank_id'                 => 'nullable|exists:banks,id',
            'transaction_category_id' => 'nullable|exists:transaction_categories,id',
        ]);

        // Otomatis assign bank Mandiri untuk QRIS jika tidak diisi
        if ($validated['metode_pembayaran'] === 'qris' && empty($validated['bank_id'])) {
            $mandiri = Bank::query()->where('nama_bank', 'like', '%Mandiri%')->first();
            $validated['bank_id'] = $mandiri?->id;
        }

        $payload = [
            'tanggal'                 => $validated['tanggal'],
            'jumlah'                  => $validated['jumlah'],
            'keterangan'              => $validated['keterangan'],
            'metode_pembayaran'       => $validated['metode_pembayaran'],
            'bank_id'                 => $validated['bank_id'] ?? null,
            'transaction_category_id' => $validated['transaction_category_id'] ?? null,
        ];

        if ($validated['tipe'] === 'masuk') {
            Income::create($payload);
            $msg = 'Mutasi uang masuk berhasil disimpan.';
        } else {
            Expense::create($payload);
            $msg = 'Mutasi uang keluar berhasil disimpan.';
        }

        return back()->with('success', $msg);
    }
}