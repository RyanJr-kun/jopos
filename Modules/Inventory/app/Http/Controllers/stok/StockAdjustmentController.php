<?php

namespace Modules\Inventory\Http\Controllers\stok;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockMovement;

class StockAdjustmentController extends Controller implements HasMiddleware
{
  /**
   * Kategori alasan penyesuaian. Dipakai buat validasi & dropdown.
   */
  public const TYPES = [
    'rusak' => 'Barang Rusak',
    'hilang' => 'Barang Hilang',
    'koreksi' => 'Koreksi Data',
    'retur_supplier' => 'Retur ke Supplier',
    'pemakaian_internal' => 'Pemakaian Internal',
  ];

  public static function middleware(): array
  {
    return [
      new Middleware('permission:view-stok-penyesuaian', only: ['index', 'show']),
      new Middleware('permission:create-stok-penyesuaian', only: ['create', 'store']),
      new Middleware('permission:delete-stok-penyesuaian', only: ['destroy']),
    ];
  }

  /**
   * Menentukan store_id yang berlaku untuk request ini.
   * Staff biasa terkunci ke store_id mereka sendiri; user dengan
   * permission 'view-toko-gudang' boleh pilih store lewat query/input.
   */
  private function resolveStoreId(Request $request): int
  {
    $user = Auth::user();

    if ($user->can('view-toko-gudang')) {
      return (int) $request->input('store_id', $user->store_id);
    }

    return (int) $user->store_id;
  }

  /**
   * Menampilkan riwayat penyesuaian stok.
   */
  public function index(Request $request)
  {
    $query = StockAdjustment::with(['user', 'store'])
      ->withCount('details')
      ->latest('tanggal_penyesuaian');

    if (Auth::user()->can('view-toko-gudang')) {
      if ($request->filled('store_id')) {
        $query->where('store_id', $request->store_id);
      }
    } else {
      $query->where('store_id', Auth::user()->store_id);
    }

    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('kode_penyesuaian', 'like', "%{$search}%")->orWhereHas('user', fn($qUser) => $qUser->where('username', 'like', "%{$search}%"));
      });
    }

    if ($request->filled('start_date') && $request->filled('end_date')) {
      $query->whereBetween('tanggal_penyesuaian', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    }

    $stocks = $query->paginate(15)->withQueryString();

    // Ringkasan stat cards — scoped sama filter store yang sama
    $statsQuery = StockAdjustment::query();
    if (Auth::user()->can('view-toko-gudang')) {
      if ($request->filled('store_id')) {
        $statsQuery->where('store_id', $request->store_id);
      }
    } else {
      $statsQuery->where('store_id', Auth::user()->store_id);
    }

    $stats = [
      'total' => (clone $statsQuery)->count(),
      'bulan_ini' => (clone $statsQuery)->whereMonth('tanggal_penyesuaian', now()->month)->whereYear('tanggal_penyesuaian', now()->year)->count(),
      'total_masuk' => (clone $statsQuery)
        ->withSum(['details as masuk_sum' => fn($q) => $q->where('jumlah', '>', 0)], 'jumlah')
        ->get()
        ->sum('masuk_sum'),
      'total_keluar' => abs(
        (clone $statsQuery)
          ->withSum(['details as keluar_sum' => fn($q) => $q->where('jumlah', '<', 0)], 'jumlah')
          ->get()
          ->sum('keluar_sum'),
      ),
    ];

    $stores = Auth::user()->can('view-toko-gudang') ? Store::orderBy('name_toko')->get() : null;

    return view('inventory::inventaris.adjustment.index', compact('stocks', 'stats', 'stores'));
  }

  /**
   * Menampilkan form untuk membuat penyesuaian stok baru.
   */
  public function create()
  {
    $stores = [];

    // Sesuaikan dengan nama permission yang Anda gunakan (view-all-store atau view-toko-gudang)
    if (Auth::user()->can('view-toko-gudang')) {
      $stores = Store::all(); // Ambil semua data toko. Bisa ditambah ->where('is_active', true) jika ada status aktif
    }

    return view('inventory::inventaris.adjustment.create', [
      'types' => self::TYPES,
      'stores' => $stores,
    ]);
  }

  /**
   * Menyimpan penyesuaian stok baru ke database.
   */
  public function store(Request $request)
  {
    $storeId = $this->resolveStoreId($request);

    $validated = $request->validate([
      'catatan' => 'nullable|string|max:1000',
      'items' => 'required|array|min:1',
      'items.*.product_id' => 'required|exists:products,id',
      'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
      'items.*.type' => 'required|in:' . implode(',', array_keys(self::TYPES)),
      'items.*.arah' => 'required|in:in,out',
      'items.*.jumlah' => 'required|integer|min:1', // magnitude, bukan signed
      'items.*.alasan' => 'required|string|max:255',
    ]);

    try {
      $penyesuaian = DB::transaction(function () use ($validated, $storeId) {
        $penyesuaian = StockAdjustment::create([
          'store_id' => $storeId,
          'kode_penyesuaian' => StockAdjustment::generateKode(),
          'tanggal_penyesuaian' => now(),
          'user_id' => Auth::id(),
          'catatan' => $validated['catatan'] ?? null,
        ]);

        foreach ($validated['items'] as $itemData) {
          $this->applyAdjustmentItem($penyesuaian, $storeId, $itemData);
        }

        return $penyesuaian;
      });

      return redirect()->route('stok-penyesuaian.show', $penyesuaian->kode_penyesuaian)->with('success', 'Penyesuaian stok berhasil disimpan.');
    } catch (\Exception $e) {
      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Terapkan satu item penyesuaian: kunci baris stok, update qty,
   * catat detail adjustment, dan catat stock movement.
   */
  private function applyAdjustmentItem(StockAdjustment $penyesuaian, int $storeId, array $itemData): void
  {
    $jumlahMagnitude = (int) $itemData['jumlah'];
    $signedJumlah = $itemData['arah'] === 'in' ? $jumlahMagnitude : -$jumlahMagnitude;

    // Lock row ProductStock biar aman dari race condition
    $stock = ProductStock::query()
      ->where('product_id', $itemData['product_id'])
      ->where('product_variant_id', $itemData['product_variant_id'] ?? null)
      ->where('store_id', $storeId)
      ->lockForUpdate()
      ->first();

    if (!$stock) {
      $stock = ProductStock::create([
        'product_id' => $itemData['product_id'],
        'product_variant_id' => $itemData['product_variant_id'] ?? null,
        'store_id' => $storeId,
        'qty' => 0,
      ]);
    }

    $stokSebelum = $stock->qty;
    $stokSetelah = $stokSebelum + $signedJumlah;

    if ($stokSetelah < 0) {
      throw new \Exception("Stok tidak mencukupi untuk produk ID {$itemData['product_id']} (stok saat ini: {$stokSebelum}, diminta keluar: {$jumlahMagnitude}).");
    }

    $stock->update(['qty' => $stokSetelah]);

    $detail = $penyesuaian->details()->create([
      'product_id' => $itemData['product_id'],
      'product_variant_id' => $itemData['product_variant_id'] ?? null,
      'type' => $itemData['type'],
      'jumlah' => $signedJumlah,
      'stok_sebelum' => $stokSebelum,
      'stok_setelah' => $stokSetelah,
      'alasan' => $itemData['alasan'],
    ]);

    StockMovement::create([
      'store_id' => $storeId,
      'product_id' => $itemData['product_id'],
      'product_variant_id' => $itemData['product_variant_id'] ?? null,
      'qty' => $signedJumlah,
      'stok_sebelum' => $stokSebelum,
      'stok_setelah' => $stokSetelah,
      'type' => StockMovement::TYPE_ADJUSTMENT,
      'keterangan' => self::TYPES[$itemData['type']] . ': ' . $itemData['alasan'],
      'user_id' => Auth::id(),
      'referensi_type' => StockAdjustment::class,
      'referensi_id' => $penyesuaian->id,
    ]);
  }

  /**
   * Menampilkan detail dari riwayat penyesuaian stok.
   */
  public function show(string $kode_penyesuaian)
  {
    $penyesuaian = StockAdjustment::where('kode_penyesuaian', $kode_penyesuaian)->firstOrFail();

    // IDOR guard: staff non-'view-toko-gudang' cuma boleh liat adjustment tokonya sendiri
    if (!Auth::user()->can('view-toko-gudang') && $penyesuaian->store_id !== Auth::user()->store_id) {
      abort(403, 'Anda tidak memiliki akses ke penyesuaian stok toko lain.');
    }

    $penyesuaian->load(['user', 'store', 'details.produk.unit', 'details.variant']);

    return view('inventory::inventaris.adjustment.show', [
      'title' => 'Detail Penyesuaian ' . $penyesuaian->kode_penyesuaian,
      'penyesuaian' => $penyesuaian,
    ]);
  }

  /**
   * Membatalkan & menghapus penyesuaian stok, serta mengembalikan stok produk.
   */
  public function destroy(StockAdjustment $stok_penyesuaian)
  {
    if (!Auth::user()->can('view-toko-gudang') && $stok_penyesuaian->store_id !== Auth::user()->store_id) {
      abort(403, 'Anda tidak memiliki akses untuk membatalkan penyesuaian toko lain.');
    }

    try {
      DB::transaction(function () use ($stok_penyesuaian) {
        foreach ($stok_penyesuaian->details as $detail) {
          $stock = ProductStock::query()
            ->where('product_id', $detail->product_id)
            ->where('product_variant_id', $detail->product_variant_id)
            ->where('store_id', $stok_penyesuaian->store_id)
            ->lockForUpdate()
            ->first();

          if (!$stock) {
            continue; // Produk/stok record sudah tidak ada, skip
          }

          $stokSebelum = $stock->qty;
          // Kebalikan dari efek adjustment semula
          $stokSetelah = $stokSebelum - $detail->jumlah;

          $stock->update(['qty' => $stokSetelah]);

          StockMovement::create([
            'store_id' => $stok_penyesuaian->store_id,
            'product_id' => $detail->product_id,
            'product_variant_id' => $detail->product_variant_id,
            'qty' => -$detail->jumlah,
            'stok_sebelum' => $stokSebelum,
            'stok_setelah' => $stokSetelah,
            'type' => StockMovement::TYPE_ADJUSTMENT,
            'keterangan' => 'Pembatalan penyesuaian ' . $stok_penyesuaian->kode_penyesuaian,
            'user_id' => Auth::id(),
            'referensi_type' => StockAdjustment::class,
            'referensi_id' => $stok_penyesuaian->id,
          ]);
        }

        $stok_penyesuaian->delete();
      });

      return redirect()->route('stok-penyesuaian.index')->with('success', 'Penyesuaian stok berhasil dibatalkan dan stok produk telah dikembalikan.');
    } catch (\Exception $e) {
      return back()->with('error', 'Terjadi kesalahan saat membatalkan penyesuaian: ' . $e->getMessage());
    }
  }
}
