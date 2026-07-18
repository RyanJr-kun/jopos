<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CashFlow;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KeuanganController extends Controller
{
  // =========================================================================
  // DASHBOARD UTAMA
  // =========================================================================

  public function index(Request $request)
  {
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

    // --- Filter metode pembayaran ---
    // UI (pills, ?metode=...) masih kirim lowercase (tunai|transfer|qris),
    // tapi kolom metode_pembayaran di cash_flows disimpan UPPERCASE (lihat CashFlow::getPaymentMethods()).
    $metodePembayaran = $request->input('metode'); // tunai | transfer | qris
    $accountId = $request->input('account_id');
    $metodeDb = $metodePembayaran ? strtoupper($metodePembayaran) : null;

    // --- Ringkasan (Income & Expense) ---
    $incomeQuery = $this->baseQuery($startDate, $endDate, $metodeDb, $accountId)->income();
    $expenseQuery = $this->baseQuery($startDate, $endDate, $metodeDb, $accountId)->expense();

    $totalIncome = (clone $incomeQuery)->sum('nominal');
    $totalExpense = (clone $expenseQuery)->sum('nominal');
    $labaRugi = $totalIncome - $totalExpense;
    $totalTransaksi = (clone $incomeQuery)->count() + (clone $expenseQuery)->count();

    // --- Breakdown metode pembayaran (untuk card detail mutasi) ---
    // key di-lowercase supaya cocok dengan array $metodes ('tunai','transfer','qris') di view.
    $breakdownIncome = $this->baseQuery($startDate, $endDate, null, null)
      ->income()
      ->select('metode_pembayaran', DB::raw('SUM(nominal) as total'), DB::raw('COUNT(*) as jumlah_transaksi'))
      ->groupBy('metode_pembayaran')
      ->get()
      ->keyBy(fn($row) => strtolower($row->metode_pembayaran));

    $breakdownExpense = $this->baseQuery($startDate, $endDate, null, null)
      ->expense()
      ->select('metode_pembayaran', DB::raw('SUM(nominal) as total'), DB::raw('COUNT(*) as jumlah_transaksi'))
      ->groupBy('metode_pembayaran')
      ->get()
      ->keyBy(fn($row) => strtolower($row->metode_pembayaran));

    // --- Saldo per rekening bank ---
    // Sekarang ikut memperhitungkan transfer (keluar dari account_id, masuk ke to_account_id),
    // bukan cuma income - expense seperti sebelumnya.
    $periodFilter = fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate])->aktif();

    $incomeByBank = $periodFilter(CashFlow::query()->income())
      ->select('account_id', DB::raw('SUM(nominal) as total'))
      ->groupBy('account_id')
      ->pluck('total', 'account_id');
    $expenseByBank = $periodFilter(CashFlow::query()->expense())
      ->select('account_id', DB::raw('SUM(nominal) as total'))
      ->groupBy('account_id')
      ->pluck('total', 'account_id');
    $transferOutByBank = $periodFilter(CashFlow::query()->transfer())
      ->select('account_id', DB::raw('SUM(nominal) as total'))
      ->groupBy('account_id')
      ->pluck('total', 'account_id');
    $transferInByBank = $periodFilter(CashFlow::query()->transfer())
      ->select('to_account_id', DB::raw('SUM(nominal) as total'))
      ->groupBy('to_account_id')
      ->pluck('total', 'to_account_id');

    $saldoPerBank = Account::query()
      ->where('is_active', true)
      ->orderBy('account_name')
      ->get()
      ->map(function ($account) use ($incomeByBank, $expenseByBank, $transferOutByBank, $transferInByBank) {
        $totalMasuk = ($incomeByBank[$account->id] ?? 0) + ($transferInByBank[$account->id] ?? 0);
        $totalKeluar = ($expenseByBank[$account->id] ?? 0) + ($transferOutByBank[$account->id] ?? 0);

        return [
          'account' => $account,
          'total_masuk' => $totalMasuk,
          'total_keluar' => $totalKeluar,
          'saldo' => $totalMasuk - $totalKeluar,
        ];
      });

    // --- Transaksi terbaru (income + expense, dari satu tabel, tidak perlu union lagi) ---
    $recentTransactions = $this->baseQuery($startDate, $endDate, $metodeDb, $accountId)
      ->whereIn('type', [CashFlow::TYPE_INCOME, CashFlow::TYPE_EXPENSE])
      ->with('account')
      ->latest('tanggal')
      ->limit(15)
      ->get();

    // --- Data grafik (harian) ---
    $incomePerHari = $this->baseQuery($startDate, $endDate, $metodeDb, $accountId)
      ->income()
      ->groupBy('date')
      ->orderBy('date')
      ->get([DB::raw('DATE(tanggal) as date'), DB::raw('SUM(nominal) as total')])
      ->pluck('total', 'date');

    $expensePerHari = $this->baseQuery($startDate, $endDate, $metodeDb, $accountId)
      ->expense()
      ->groupBy('date')
      ->orderBy('date')
      ->get([DB::raw('DATE(tanggal) as date'), DB::raw('SUM(nominal) as total')])
      ->pluck('total', 'date');

    $period = CarbonPeriod::create($startDate, $endDate);
    $chartLabels = collect($period)->map(fn($d) => $d->isoFormat('D MMM'));
    $incomeData = collect($period)->map(fn($d) => (int) ($incomePerHari[$d->format('Y-m-d')] ?? 0));
    $expenseData = collect($period)->map(fn($d) => (int) ($expensePerHari[$d->format('Y-m-d')] ?? 0));

    // --- Daftar bank untuk filter & modal ---
    $accounts = Account::query()->where('tipe_akun', 'bank')->get();

    return view('content.finance.index', [
      'title' => 'Dashboard Keuangan',
      'startDate' => $startDate,
      'endDate' => $endDate,
      'metodePembayaran' => $metodePembayaran,
      'bankIdFilter' => $accountId,
      'totalIncome' => $totalIncome,
      'totalExpense' => $totalExpense,
      'labaRugi' => $labaRugi,
      'totalTransaksi' => $totalTransaksi,
      'breakdownIncome' => $breakdownIncome,
      'breakdownExpense' => $breakdownExpense,
      'saldoPerBank' => $saldoPerBank,
      'recentTransactions' => $recentTransactions,
      'accounts' => $accounts,
      'chartData' => [
        'labels' => $chartLabels,
        'income' => $incomeData,
        'expense' => $expenseData,
      ],
    ]);
  }

  /**
   * Query dasar CashFlow ter-filter tanggal/metode/bank, hanya transaksi aktif (belum dibatalkan).
   */
  protected function baseQuery(string $startDate, string $endDate, ?string $metodeDb, $accountId)
  {
    return CashFlow::query()
      ->whereBetween('tanggal', [$startDate, $endDate])
      ->aktif()
      ->when($metodeDb, fn($q) => $q->where('metode_pembayaran', $metodeDb))
      ->when($accountId, fn($q) => $q->where('account_id', $accountId));
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
      'account_name' => 'required|string|max:100',
      'nomor_rekening' => 'required|string|max:50',
      'nama_pemilik' => 'required|string|max:100',
      'logo_bank' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
      'is_active' => 'boolean',
    ]);

    if ($request->hasFile('logo_bank')) {
      $file = $request->file('logo_bank');
      $filename = 'accounts/' . Str::slug($validated['account_name']) . '-' . time() . '.' . $file->getClientOriginalExtension();
      $validated['logo_bank'] = Storage::disk('r2')->putFileAs('', $file, $filename) ? $filename : null;
    }

    $validated['is_active'] = $request->boolean('is_active', true);

    Account::create($validated);

    return back()->with('success', 'Akun berhasil ditambahkan.');
  }

  /**
   * Update data bank. Ganti logo jika ada file baru.
   */
  public function updateBank(Request $request, Account $account)
  {
    $validated = $request->validate([
      'account_name' => 'required|string|max:100',
      'nomor_rekening' => 'required|string|max:50',
      'nama_pemilik' => 'required|string|max:100',
      'logo_bank' => 'nullable|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
      'is_active' => 'boolean',
    ]);

    if ($request->hasFile('logo_bank')) {
      // Hapus logo lama dari R2
      if ($account->logo_bank && Storage::disk('r2')->exists($account->logo_bank)) {
        Storage::disk('r2')->delete($account->logo_bank);
      }

      $file = $request->file('logo_bank');
      $filename = 'accounts/' . Str::slug($validated['account_name']) . '-' . time() . '.' . $file->getClientOriginalExtension();
      $validated['logo_bank'] = Storage::disk('r2')->putFileAs('', $file, $filename) ? $filename : null;
    } else {
      unset($validated['logo_bank']); // Jangan timpa logo lama
    }

    $validated['is_active'] = $request->boolean('is_active', true);

    $account->update($validated);

    return back()->with('success', 'Data akun berhasil diperbarui.');
  }

  /**
   * Hapus bank (soft: nonaktifkan, atau hard delete jika tidak ada transaksi).
   */
  public function destroyBank(Account $account)
  {
    // Cek transaksi sebagai bank asal ATAU bank tujuan (transfer).
    $hasTransaksi = CashFlow::query()->where('account_id', $account->id)->orWhere('to_account_id', $account->id)->exists();

    if ($hasTransaksi) {
      $account->update(['is_active' => false]);

      return back()->with('info', 'Bank memiliki riwayat transaksi, dinonaktifkan saja.');
    }

    if ($account->logo_bank && Storage::disk('r2')->exists($account->logo_bank)) {
      Storage::disk('r2')->delete($account->logo_bank);
    }

    $account->delete();

    return back()->with('success', 'akun berhasil dihapus.');
  }

  // =========================================================================
  // MUTASI — Uang masuk & keluar ke rekening / kasir (quick-add dari dashboard)
  // =========================================================================

  /**
   * Simpan mutasi cepat (income atau expense) langsung ke cash_flows.
   *
   * Request params:
   *  - tipe             : 'masuk' | 'keluar'
   *  - tanggal          : date
   *  - jumlah           : numeric
   *  - keterangan       : string
   *  - metode_pembayaran: 'tunai' | 'transfer' | 'qris' (lowercase dari form, disimpan uppercase)
   *  - account_id          : int|null (wajib jika transfer atau qris)
   *  - transaction_category_id : int|null
   *
   * NOTE: 'store' sudah tidak ada di CashFlow::getPaymentMethods(), jadi opsi itu dihapus
   * dari form Mutasi. Pastikan generator `referensi` di bawah ini disamakan dengan konvensi
   * yang dipakai CashFlowController (jika berbeda).
   */
  public function storeMutasi(Request $request)
  {
    $validated = $request->validate([
      'tipe' => 'required|in:masuk,keluar',
      'tanggal' => 'required|date',
      'jumlah' => 'required|numeric|min:1',
      'keterangan' => 'required|string|max:500',
      'metode_pembayaran' => 'required|in:tunai,transfer,qris',
      'account_id' => 'nullable|exists:accounts,id',
      'transaction_category_id' => 'nullable|exists:transaction_categories,id',
    ]);

    $metode = strtoupper($validated['metode_pembayaran']);
    $accountId = $validated['account_id'] ?? null;

    // Otomatis assign bank Mandiri untuk QRIS jika tidak diisi
    if ($metode === 'QRIS' && empty($accountId)) {
      $accountId = Account::query()->where('account_name', 'like', '%Mandiri%')->value('id');
    }

    $payload = [
      'type' => $validated['tipe'] === 'masuk' ? CashFlow::TYPE_INCOME : CashFlow::TYPE_EXPENSE,
      'tanggal' => $validated['tanggal'],
      'nominal' => $validated['jumlah'],
      'keterangan' => $validated['keterangan'],
      'metode_pembayaran' => $metode,
      'account_id' => $accountId,
      'transaction_category_id' => $validated['transaction_category_id'] ?? null,
      'referensi' => $this->generateReferensi($validated['tipe']),
      'user_id' => Auth::id(),
      'store_id' => Auth::user()?->employee?->store_id,
    ];

    CashFlow::create($payload);

    $msg = $validated['tipe'] === 'masuk' ? 'Mutasi uang masuk berhasil disimpan.' : 'Mutasi uang keluar berhasil disimpan.';

    return back()->with('success', $msg);
  }

  /**
   * Generator referensi sederhana untuk mutasi cepat.
   * TODO: samakan dengan konvensi referensi_otomatis di CashFlowController kalau berbeda.
   */
  protected function generateReferensi(string $tipe): string
  {
    $prefix = $tipe === 'masuk' ? 'MSK' : 'KLR';

    return $prefix . '-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5));
  }
}
