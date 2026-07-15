<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\CashFlow;
use App\Models\Store;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CashFlowController extends Controller implements HasMiddleware
{
  private const TYPES = [CashFlow::TYPE_INCOME, CashFlow::TYPE_EXPENSE, CashFlow::TYPE_TRANSFER];

  public static function middleware(): array
  {
    return [
      new Middleware('permission:view-cash-flow', only: ['index', 'getjson']),
      new Middleware('permission:create-cash-flow', only: ['create', 'store', 'quickStore']),
      new Middleware('permission:edit-cash-flow', only: ['edit', 'update']),
      new Middleware('permission:delete-cash-flow', only: ['destroy']),
    ];
  }

  private function resolveType(string $type): string
  {
    abort_unless(in_array($type, self::TYPES, true), 404);

    return $type;
  }

  private function viewFor(string $type): string
  {
    return match ($type) {
      CashFlow::TYPE_INCOME => 'content.finance.pemasukan',
      CashFlow::TYPE_EXPENSE => 'content.finance.pengeluaran',
      CashFlow::TYPE_TRANSFER => 'content.finance.transfer',
    };
  }

  /**
   * Request dianggap AJAX/offcanvas kalau header X-Requested-With ada
   * (otomatis terkirim oleh fetch()/axios kalau di-set manual, lihat JS di cash-flow-form.blade.php).
   */
  private function wantsJson(Request $request): bool
  {
    return $request->ajax() || $request->wantsJson();
  }

  public function index(Request $request, string $type)
  {
    $type = $this->resolveType($type);

    $query = CashFlow::with(['transaction_category', 'user', 'bank', 'bankTujuan', 'store'])
      ->where('type', $type)
      ->latest('tanggal');

    $user = Auth::user();
    $stores = null;

    if ($user->can('view-toko-gudang')) {
      $stores = Store::all();
      if ($request->filled('store_id')) {
        $query->where('store_id', $request->input('store_id'));
      }
    } else {
      $storeId = $user->employee?->store_id;
      $query->where('store_id', $storeId);
    }

    $kategoriFilters = collect();
    $allKategoris = collect();

    if ($type !== CashFlow::TYPE_TRANSFER) {
      $kategoriFilters = TransactionCategory::query()->where('type', $type)->whereHas('cashFlows', fn($q) => $q->where('type', $type))->orderBy('name', 'asc')->get();

      $allKategoris = TransactionCategory::query()->where('type', $type)->where('status', 1)->orderBy('name', 'asc')->get();
    }

    if ($request->filled('search')) {
      $search = $request->input('search');
      $query->where(function ($q) use ($search) {
        $q->where('keterangan', 'like', "%{$search}%")->orWhere('referensi', 'like', "%{$search}%");
      });
    }

    if ($request->filled('kategori_id')) {
      $query->where('transaction_category_id', $request->input('kategori_id'));
    }

    $cashFlows = $query->paginate(15)->withQueryString();
    $totalNominal = (clone $query)->sum('nominal');

    $viewData = [
      'title' => $type,
      'type' => $type,
      'cashFlows' => $cashFlows,
      'kategoriFilters' => $kategoriFilters,
      'allKategoris' => $allKategoris,
      'referensi_otomatis' => $this->generateReferenceNumber($type),
      'stores' => $stores,
      'banks' => Bank::all(),
      'totalNominal' => $totalNominal,
    ];

    $view = $this->viewFor($type);

    if ($this->wantsJson($request) && $request->query('fragment') === 'table') {
      $html = view($view, $viewData)->fragment('cash-flow-table-area');

      return response()->json([
        'html' => $html,
        'total' => $cashFlows->total(),
      ]);
    }

    return view($view, $viewData);
  }

  private function generateReferenceNumber(string $type): string
  {
    $date = now()->format('Ymd');
    $prefixMap = [
      CashFlow::TYPE_INCOME => 'IN-',
      CashFlow::TYPE_EXPENSE => 'EX-',
      CashFlow::TYPE_TRANSFER => 'TR-',
    ];
    $prefix = $prefixMap[$type] . $date . '-';

    $last = CashFlow::query()
      ->where('type', $type)
      ->where('referensi', 'like', $prefix . '%')
      ->latest('referensi')
      ->first();

    $sequence = 1;
    if ($last) {
      $sequence = ((int) substr($last->referensi, -4)) + 1;
    }

    return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
  }

  /**
   * Mengembalikan HTML form (untuk di-load ke dalam offcanvas via fetch).
   */
  public function create(Request $request, string $type)
  {
    $type = $this->resolveType($type);

    $data = [
      'type' => $type,
      'cashFlow' => new CashFlow(['type' => $type]),
      'kategoris' => $type === CashFlow::TYPE_TRANSFER ? collect() : TransactionCategory::where('type', $type)->where('status', 1)->orderBy('name')->get(),
      'banks' => Bank::all(),
      'referensi_otomatis' => $this->generateReferenceNumber($type),
    ];

    if ($this->wantsJson($request)) {
      return response()->json([
        'html' => view('content.finance._form', $data)->render(),
      ]);
    }

    return view('content.finance._form', $data);
  }

  public function edit(Request $request, string $type, CashFlow $cash_flow)
  {
    $type = $this->resolveType($type);

    $data = [
      'type' => $type,
      'cashFlow' => $cash_flow,
      'kategoris' => $type === CashFlow::TYPE_TRANSFER ? collect() : TransactionCategory::where('type', $type)->where('status', 1)->orderBy('name')->get(),
      'banks' => Bank::all(),
    ];

    if ($this->wantsJson($request)) {
      return response()->json([
        'html' => view('content.finance._form', $data)->render(),
      ]);
    }

    return view('content.finance._form', $data);
  }

  private function rulesFor(Request $request, string $type, ?CashFlow $cashFlow = null): array
  {
    $rules = [
      'tanggal' => 'required|date_format:Y-m-d|before_or_equal:today',
      'nominal' => 'required|numeric|min:1',
      'metode_pembayaran' => 'required|in:TUNAI,TRANSFER,QRIS',
      'bank_id' => 'nullable|required_if:metode_pembayaran,TRANSFER|exists:banks,id',
      'referensi' => ['nullable', 'string', 'max:100', Rule::unique('cash_flows', 'referensi')->ignore($cashFlow?->id)],
      'keterangan' => 'nullable|string|max:255',
      'description' => 'nullable|string|max:1000',
      'bukti' => 'nullable|image|max:2048',
    ];

    if ($type === CashFlow::TYPE_TRANSFER) {
      $rules['keterangan'] = 'nullable|string|max:255'; // auto-diisi "Transfer Internal" kalau kosong
      $rules['metode_pembayaran_tujuan'] = 'required|in:TUNAI,TRANSFER,QRIS';
      $rules['bank_id_tujuan'] = 'nullable|required_if:metode_pembayaran_tujuan,TRANSFER|exists:banks,id';
    } else {
      $rules['keterangan'] = 'required|string|max:255';
      $rules['transaction_category_id'] = ['required', Rule::exists('transaction_categories', 'id')->where('type', $type)];
    }

    return $rules;
  }

  /**
   * Untuk transfer: pastikan rekening asal & tujuan tidak sama persis.
   */
  private function assertTransferAccountsDiffer(array $data): void
  {
    $sameMethod = $data['metode_pembayaran'] === $data['metode_pembayaran_tujuan'];
    $sameBank = ($data['bank_id'] ?? null) == ($data['bank_id_tujuan'] ?? null);

    if ($sameMethod && $sameBank) {
      throw ValidationException::withMessages([
        'bank_id_tujuan' => 'Rekening/metode asal dan tujuan tidak boleh sama.',
      ]);
    }
  }

  /**
   * Logic inti simpan cash flow baru — dipakai bareng oleh store() (halaman CRUD)
   * dan quickStore() (modal Mutasi di Dashboard Keuangan).
   *
   * @throws ValidationException
   */
  private function persist(Request $request, string $type): CashFlow
  {
    $validated = $request->validate($this->rulesFor($request, $type));

    if ($type === CashFlow::TYPE_TRANSFER) {
      $this->assertTransferAccountsDiffer($validated);
    }

    $storeId = Auth::user()->employee?->store_id;

    if (!$storeId) {
      throw ValidationException::withMessages([
        'store_id' => 'Akun Anda belum terdaftar di toko (store) manapun.',
      ]);
    }

    $validated['type'] = $type;
    $validated['store_id'] = $storeId;
    $validated['user_id'] = Auth::id();
    $validated['referensi'] = $validated['referensi'] ?? $this->generateReferenceNumber($type);

    if ($type === CashFlow::TYPE_TRANSFER) {
      $validated['transaction_category_id'] = null;
      $validated['keterangan'] = $validated['keterangan'] ?? 'Transfer Internal';
    }

    if ($request->hasFile('bukti')) {
      $validated['bukti'] = $request->file('bukti')->store('cash-flows', 'r2');
    }

    return CashFlow::create($validated);
  }

  public function store(Request $request, string $type)
  {
    $type = $this->resolveType($type);

    try {
      $cashFlow = $this->persist($request, $type);
    } catch (ValidationException $e) {
      if ($this->wantsJson($request)) {
        throw $e; // Laravel otomatis balas 422 JSON kalau request expects JSON
      }

      return back()
        ->withInput()
        ->with('error', collect($e->errors())->flatten()->first());
    }

    $message = CashFlow::typeLabel($type) . ' baru berhasil ditambahkan!';

    if ($this->wantsJson($request)) {
      return response()->json(['success' => true, 'message' => $message, 'data' => $cashFlow]);
    }

    return redirect()
      ->route('financial.cash-flows.index', ['type' => $type])
      ->with('success', $message);
  }

  /**
   * Quick-add dari modal "Mutasi" di Dashboard Keuangan.
   * Beda dari store(): type datang dari field form (bukan segmen URL), dan redirect balik ke dashboard.
   */
  public function quickStore(Request $request)
  {
    $type = $this->resolveType($request->input('type', ''));

    try {
      $this->persist($request, $type);
    } catch (ValidationException $e) {
      return back()
        ->withInput()
        ->with('error', collect($e->errors())->flatten()->first());
    }

    return redirect()
      ->route('keuangan')
      ->with('success', CashFlow::typeLabel($type) . ' berhasil dicatat!');
  }

  public function getjson(CashFlow $cash_flow)
  {
    return response()->json($cash_flow);
  }

  public function update(Request $request, string $type, CashFlow $cash_flow)
  {
    $type = $this->resolveType($type);

    $validated = $request->validate($this->rulesFor($request, $type, $cash_flow));

    if ($type === CashFlow::TYPE_TRANSFER) {
      $this->assertTransferAccountsDiffer($validated);
    }

    $storeId = Auth::user()->employee?->store_id;

    if ($cash_flow->store_id != $storeId && !Auth::user()->can('view-toko-gudang')) {
      $message = 'Gagal: Anda tidak memiliki akses untuk mengubah data dari toko lain.';

      return $this->wantsJson($request) ? response()->json(['message' => $message], 403) : back()->with('error', $message);
    }

    if ($type === CashFlow::TYPE_TRANSFER) {
      $validated['transaction_category_id'] = null;
      $validated['keterangan'] = $validated['keterangan'] ?? 'Transfer Internal';
    }

    if ($request->hasFile('bukti')) {
      if ($cash_flow->bukti) {
        Storage::disk('r2')->delete($cash_flow->bukti);
      }
      $validated['bukti'] = $request->file('bukti')->store('cash-flows', 'r2');
    }

    $cash_flow->update($validated);

    $message = CashFlow::typeLabel($type) . ' berhasil diperbarui!';

    if ($this->wantsJson($request)) {
      return response()->json(['success' => true, 'message' => $message, 'data' => $cash_flow]);
    }

    return redirect()
      ->route('financial.cash-flows.index', ['type' => $type])
      ->with('success', $message);
  }

  public function destroy(Request $request, string $type, CashFlow $cash_flow)
  {
    $type = $this->resolveType($type);

    if ($cash_flow->bukti) {
      Storage::disk('r2')->delete($cash_flow->bukti);
    }

    $cash_flow->delete();

    $message = 'Data berhasil dihapus!';

    if ($this->wantsJson($request)) {
      return response()->json(['success' => true, 'message' => $message]);
    }

    return redirect()
      ->route('financial.cash-flows.index', ['type' => $type])
      ->with('success', $message);
  }
}
