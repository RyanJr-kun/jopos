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
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\SerialNumber;
use Modules\Inventory\Models\StockAdjustment;
use Modules\Inventory\Models\StockMovement;
use Modules\Inventory\Models\StockTransfer;

class StockTransferController extends Controller implements HasMiddleware
{
  public static function middleware(): array
  {
    return [new Middleware('permission:view-stok-transfer', only: ['index', 'data', 'show']), new Middleware('permission:create-stok-transfer', only: ['create', 'store', 'approve', 'reject'])];
  }

  /**
   * Halaman utama riwayat transfer stok.
   * Tabel di-render server-side untuk load pertama (tanpa JS pun tetap jalan),
   * lalu di-refresh via AJAX oleh method data().
   */
  public function index(Request $request)
  {
    $transfers = $this->buildQuery($request)->paginate(15)->withQueryString();

    return view('inventory::inventaris.transfer.index', [
      'transfers' => $transfers,
      'stores' => Store::orderBy('name_toko')->get(),
    ]);
  }

  /**
   * Endpoint AJAX: balikin partial tabel sesuai filter, dipanggil dari JS
   * pakai fetch + debounce tiap kali filter berubah.
   */
  public function data(Request $request)
  {
    $transfers = $this->buildQuery($request)->paginate(15)->withQueryString();

    return view('inventory::inventaris.transfer._table', compact('transfers'));
  }

  /**
   * Form untuk membuat transfer stok baru.
   */
  public function create()
  {
    $user = Auth::user();

    // Toko asal: kalau bukan view-toko-gudang, terkunci ke toko sendiri
    $storesAsal = $user->can('view-toko-gudang') ? Store::orderBy('name_toko')->get() : Store::where('id', $user->employee->store_id)->get();

    $storesTujuan = Store::orderBy('name_toko')->get();

    return view('inventory::inventaris.transfer.create', [
      'storesAsal' => $storesAsal,
      'storesTujuan' => $storesTujuan,
      'defaultStoreAsalId' => $user->can('view-toko-gudang') ? null : $user->employee->store_id,
    ]);
  }

  /**
   * Menyimpan transfer baru & langsung mengirim (stok toko asal dikurangi saat itu juga).
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'store_asal_id' => 'required|exists:stores,id',
      'store_tujuan_id' => 'required|different:store_asal_id|exists:stores,id',
      'catatan_kirim' => 'nullable|string|max:1000',
      'items' => 'required|array|min:1',
      'items.*.product_id' => 'required|exists:products,id',
      'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
      'items.*.qty_kirim' => 'required|integer|min:1',
      'items.*.serial_numbers' => 'nullable|array',
      'items.*.serial_numbers.*' => 'exists:serial_numbers,nomor_seri',
    ]);

    $user = Auth::user();

    if (!$user->can('view-toko-gudang') && (int) $validated['store_asal_id'] !== (int) $user->employee->store_id) {
      return back()->withInput()->with('error', 'Anda hanya dapat mengirim transfer dari toko Anda sendiri.');
    }

    try {
      $transfer = DB::transaction(function () use ($validated, $user) {
        $transfer = StockTransfer::create([
          'kode_transfer' => StockTransfer::generateKode(),
          'store_asal_id' => $validated['store_asal_id'],
          'store_tujuan_id' => $validated['store_tujuan_id'],
          'tanggal_kirim' => now(),
          'user_kirim_id' => $user->id,
          'status' => StockTransfer::STATUS_DIKIRIM,
          'catatan_kirim' => $validated['catatan_kirim'] ?? null,
        ]);

        foreach ($validated['items'] as $itemData) {
          $this->processOutgoingItem($transfer, $itemData);
        }

        return $transfer;
      });

      return redirect()->route('stok-transfer.show', $transfer->kode_transfer)->with('success', 'Transfer stok berhasil dikirim. Menunggu konfirmasi penerimaan dari toko tujuan.');
    } catch (\Exception $e) {
      return back()
        ->withInput()
        ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Proses satu item pengiriman: lock stok toko asal, validasi cukup,
   * kurangi stok, catat item transfer & stock movement.
   */
  private function processOutgoingItem(StockTransfer $transfer, array $itemData): void
  {
    $qtyKirim = (int) $itemData['qty_kirim'];

    $stock = ProductStock::query()
      ->where('product_id', $itemData['product_id'])
      ->where('product_variant_id', $itemData['product_variant_id'] ?? null)
      ->where('store_id', $transfer->store_asal_id)
      ->lockForUpdate()
      ->first();

    $stokSebelum = $stock->qty ?? 0;

    if (!$stock || $stokSebelum < $qtyKirim) {
      $produkNama = Product::find($itemData['product_id'])->name_product ?? "ID {$itemData['product_id']}";
      throw new \Exception("Stok tidak mencukupi untuk produk \"{$produkNama}\" (stok saat ini: {$stokSebelum}, diminta kirim: {$qtyKirim}).");
    }

    $stokSetelah = $stokSebelum - $qtyKirim;
    $stock->update(['qty' => $stokSetelah]);

    $transferDetail = $transfer->details()->create([
      'product_id' => $itemData['product_id'],
      'product_variant_id' => $itemData['product_variant_id'] ?? null,
      'qty_kirim' => $qtyKirim,
    ]);

    // ==== TAMBAHAN UNTUK NOMOR SERI ====
    if (!empty($itemData['serial_numbers'])) {
      // Ambil ID dari nomor seri yang valid
      $snIds = SerialNumber::where('product_id', $itemData['product_id'])->where('store_id', $transfer->store_asal_id)->whereIn('nomor_seri', $itemData['serial_numbers'])->pluck('id')->toArray();

      if (count($snIds) !== count($itemData['serial_numbers'])) {
        throw new \Exception('Ada nomor seri yang tidak valid atau tidak tersedia di toko asal.');
      }

      // Simpan ke tabel pivot
      $transferDetail->serialNumbers()->attach($snIds);

      // Ubah status SN menjadi Dalam Perjalanan
      SerialNumber::whereIn('id', $snIds)->update([
        'status' => 'Dalam Perjalanan', // Atau status khusus lain seperti 'Transfer Out'
      ]);
    }

    StockMovement::create([
      'store_id' => $transfer->store_asal_id,
      'product_id' => $itemData['product_id'],
      'product_variant_id' => $itemData['product_variant_id'] ?? null,
      'qty' => -$qtyKirim,
      'stok_sebelum' => $stokSebelum,
      'stok_setelah' => $stokSetelah,
      'type' => StockMovement::TYPE_TRANSFER_OUT,
      'keterangan' => "Transfer keluar ke toko tujuan (kode: {$transfer->kode_transfer})",
      'user_id' => Auth::id(),
      'referensi_type' => StockTransfer::class,
      'referensi_id' => $transfer->id,
    ]);
  }

  /**
   * Menampilkan detail transfer stok, termasuk status pengiriman/penerimaan tiap item.
   */
  public function show(StockTransfer $stock_transfer)
  {
    $user = Auth::user();

    // IDOR guard: staff non 'view-toko-gudang' cuma boleh liat transfer yang
    // melibatkan toko dia sendiri, baik sebagai asal maupun tujuan.
    if (!$user->can('view-toko-gudang')) {
      $isAsal = (int) $stock_transfer->store_asal_id === (int) $user->employee->store_id;
      $isTujuan = (int) $stock_transfer->store_tujuan_id === (int) $user->employee->store_id;

      if (!$isAsal && !$isTujuan) {
        abort(403, 'Anda tidak memiliki akses ke transfer stok ini.');
      }
    }

    $stock_transfer->load(['storeAsal', 'storeTujuan', 'userKirim', 'userTerima', 'details.produk.unit', 'details.variant', 'details.serialNumbers']);

    return view('inventory::inventaris.transfer.show', [
      'transfer' => $stock_transfer,
      'canApprove' =>
        $user->can('create-stok-transfer') &&
        $stock_transfer->status === StockTransfer::STATUS_DIKIRIM &&
        // Ubah baris di bawah ini agar wajib mengecek store_id tujuan
        (int) $stock_transfer->store_tujuan_id === (int) optional($user->employee)->store_id,
    ]);
  }

  /**
   * Konfirmasi penerimaan barang oleh toko tujuan.
   * Qty diterima per item bisa beda dari qty kirim (barang rusak/hilang di jalan).
   * Kalau ada selisih, otomatis generate StockAdjustment di toko asal buat audit trail.
   */
  public function approve(Request $request, StockTransfer $stock_transfer)
  {
    $user = Auth::user();

    if (!$user->can('view-toko-gudang') && (int) $stock_transfer->store_tujuan_id !== (int) $user->employee->store_id) {
      abort(403, 'Hanya toko tujuan yang dapat mengonfirmasi penerimaan transfer ini.');
    }

    if ($stock_transfer->status !== StockTransfer::STATUS_DIKIRIM) {
      return back()->with('error', 'Transfer ini sudah diproses sebelumnya dan tidak bisa dikonfirmasi ulang.');
    }

    $validated = $request->validate([
      'catatan_terima' => 'nullable|string|max:1000',
      'items' => 'required|array|min:1',
      'items.*.id' => 'required|exists:stock_transfer_items,id',
      'items.*.qty_diterima' => 'required|integer|min:0',
      'items.*.keterangan_selisih' => 'nullable|string|max:255',
    ]);

    try {
      DB::transaction(function () use ($validated, $stock_transfer, $user) {
        $adaSelisih = false;
        $itemSelisih = [];
        $storeTujuanId = $stock_transfer->store_tujuan_id;
        $now = now();

        // 1. Pre-fetch detail transfer agar tidak query di dalam loop
        $itemIds = collect($validated['items'])->pluck('id');
        $details = $stock_transfer->details()->whereIn('id', $itemIds)->get()->keyBy('id');

        // 2. Pre-fetch dan Lock ProductStock sekaligus dalam 1 query
        $productIds = $details->pluck('product_id')->unique();
        $existingStocks = ProductStock::query()
          ->where('store_id', $storeTujuanId)
          ->whereIn('product_id', $productIds)
          ->orderBy('product_id') // Mencegah deadlock di MySQL
          ->lockForUpdate()
          ->get()
          ->keyBy(fn($item) => $item->product_id . '_' . ($item->product_variant_id ?: 'null'));

        $movements = []; // Menampung data untuk Batch Insert

        foreach ($validated['items'] as $itemInput) {
          $detail = $details->get($itemInput['id']);
          $qtyDiterima = (int) $itemInput['qty_diterima'];

          if ($qtyDiterima > $detail->qty_kirim) {
            throw new \Exception("Qty diterima tidak boleh melebihi qty kirim ({$detail->qty_kirim}) untuk salah satu item.");
          }

          $detail->update([
            'qty_diterima' => $qtyDiterima,
            'keterangan_selisih' => $itemInput['keterangan_selisih'] ?? null,
          ]);

          // Cek stok di dalam collection (memory) alih-alih query DB
          $stockKey = $detail->product_id . '_' . ($detail->product_variant_id ?: 'null');
          $stock = $existingStocks->get($stockKey);

          // Celah Race Condition teratasi: FirstOrCreate aman karena sudah di-lock di scope transaksi
          if (!$stock) {
            $stock = ProductStock::create([
              'product_id' => $detail->product_id,
              'product_variant_id' => $detail->product_variant_id,
              'store_id' => $storeTujuanId,
              'qty' => 0,
            ]);
            $existingStocks->put($stockKey, $stock); // Daftarkan ke memori
          }

          $stokSebelum = $stock->qty;
          $stokSetelah = $stokSebelum + $qtyDiterima;
          $stock->update(['qty' => $stokSetelah]);

          // Kumpulkan data movement untuk di-insert sekaligus
          $movements[] = [
            'store_id' => $storeTujuanId,
            'product_id' => $detail->product_id,
            'product_variant_id' => $detail->product_variant_id,
            'qty' => $qtyDiterima,
            'stok_sebelum' => $stokSebelum,
            'stok_setelah' => $stokSetelah,
            'type' => StockMovement::TYPE_TRANSFER_IN,
            'keterangan' => "Transfer masuk dari {$stock_transfer->storeAsal->name_toko} (kode: {$stock_transfer->kode_transfer})",
            'user_id' => $user->id,
            'referensi_type' => StockTransfer::class,
            'referensi_id' => $stock_transfer->id,
            'created_at' => $now,
            'updated_at' => $now,
          ];

          if ($qtyDiterima < $detail->qty_kirim) {
            $adaSelisih = true;
            $itemSelisih[] = $detail;
          }
        }

        $snIdsToTransfer = $detail->serialNumbers()->pluck('serial_numbers.id')->toArray();

        if (!empty($snIdsToTransfer)) {
          SerialNumber::whereIn('id', $snIdsToTransfer)->update([
            'store_id' => $storeTujuanId,
            'status' => 'Tersedia',
          ]);
        }
        // 3. Eksekusi Batch Insert StockMovement (1 query untuk banyak baris)
        if (!empty($movements)) {
          StockMovement::insert($movements);
        }

        // Pencatatan otomatis jika ada selisih (Tetap dipertahankan)
        if ($adaSelisih) {
          $adjustment = StockAdjustment::create([
            'store_id' => $stock_transfer->store_asal_id,
            'kode_penyesuaian' => StockAdjustment::generateKode(),
            'tanggal_penyesuaian' => $now,
            'user_id' => $user->id,
            'catatan' => "Selisih otomatis dari transfer {$stock_transfer->kode_transfer}",
            'sumber_type' => StockTransfer::class,
            'sumber_id' => $stock_transfer->id,
          ]);

          // Fetch stok asal sekaligus (menghindari query lagi di loop adjustment)
          $selisihProductIds = collect($itemSelisih)->pluck('product_id')->unique();
          $stokAsalList = ProductStock::query()
            ->where('store_id', $stock_transfer->store_asal_id)
            ->whereIn('product_id', $selisihProductIds)
            ->get()
            ->keyBy(fn($item) => $item->product_id . '_' . ($item->product_variant_id ?: 'null'));

          $adjustmentDetails = [];
          foreach ($itemSelisih as $detail) {
            $selisihQty = $detail->qty_kirim - $detail->qty_diterima;
            $stockKey = $detail->product_id . '_' . ($detail->product_variant_id ?: 'null');
            $stokAsalSaatIni = $stokAsalList->get($stockKey)?->qty ?? 0;

            $adjustmentDetails[] = [
              'stock_adjustment_id' => $adjustment->id,
              'product_id' => $detail->product_id,
              'product_variant_id' => $detail->product_variant_id,
              'type' => 'hilang',
              'jumlah' => -$selisihQty,
              'stok_sebelum' => $stokAsalSaatIni,
              'stok_setelah' => $stokAsalSaatIni,
              'alasan' => $detail->keterangan_selisih ?: 'Selisih diterima saat transfer stok',
              'created_at' => $now,
              'updated_at' => $now,
            ];
          }

          if (!empty($adjustmentDetails)) {
            $adjustment->details()->insert($adjustmentDetails); // Batch insert details
          }
        }

        $stock_transfer->update([
          'status' => $adaSelisih ? StockTransfer::STATUS_DITERIMA_SEBAGIAN : StockTransfer::STATUS_DITERIMA,
          'tanggal_diterima' => $now,
          'user_terima_id' => $user->id,
          'catatan_terima' => $validated['catatan_terima'] ?? null,
        ]);
      });

      return redirect()->route('stok-transfer.show', $stock_transfer->kode_transfer)->with('success', 'Penerimaan barang berhasil dikonfirmasi.');
    } catch (\Exception $e) {
      return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Tolak transfer (misal salah kirim toko tujuan). Semua stok dikembalikan ke toko asal.
   */
  public function reject(Request $request, StockTransfer $stock_transfer)
  {
    $user = Auth::user();

    if (!$user->can('view-toko-gudang') && (int) $stock_transfer->store_tujuan_id !== (int) $user->employee->store_id) {
      abort(403, 'Hanya toko tujuan yang dapat menolak transfer ini.');
    }

    if ($stock_transfer->status !== StockTransfer::STATUS_DIKIRIM) {
      return back()->with('error', 'Transfer ini sudah diproses sebelumnya.');
    }

    $validated = $request->validate([
      'catatan_terima' => 'required|string|max:1000',
    ]);

    try {
      DB::transaction(function () use ($validated, $stock_transfer, $user) {
        $storeAsalId = $stock_transfer->store_asal_id;
        $now = now();
        $movements = [];

        // 1. Pre-fetch dan Lock stok toko asal sekaligus
        $productIds = $stock_transfer->details->pluck('product_id')->unique();
        $existingStocks = ProductStock::query()
          ->where('store_id', $storeAsalId)
          ->whereIn('product_id', $productIds)
          ->orderBy('product_id')
          ->lockForUpdate()
          ->get()
          ->keyBy(fn($item) => $item->product_id . '_' . ($item->product_variant_id ?: 'null'));

        foreach ($stock_transfer->details as $detail) {
          $stockKey = $detail->product_id . '_' . ($detail->product_variant_id ?: 'null');
          $stock = $existingStocks->get($stockKey);

          $stokSebelum = $stock->qty ?? 0;
          $stokSetelah = $stokSebelum + $detail->qty_kirim;

          if ($stock) {
            $stock->update(['qty' => $stokSetelah]);
          } else {
            ProductStock::create([
              'product_id' => $detail->product_id,
              'product_variant_id' => $detail->product_variant_id,
              'store_id' => $storeAsalId,
              'qty' => $stokSetelah,
            ]);
          }

          $movements[] = [
            'store_id' => $storeAsalId,
            'product_id' => $detail->product_id,
            'product_variant_id' => $detail->product_variant_id,
            'qty' => $detail->qty_kirim,
            'stok_sebelum' => $stokSebelum,
            'stok_setelah' => $stokSetelah,
            'type' => StockMovement::TYPE_TRANSFER_IN,
            'keterangan' => "Pengembalian stok - transfer {$stock_transfer->kode_transfer} ditolak toko tujuan",
            'user_id' => $user->id,
            'referensi_type' => StockTransfer::class,
            'referensi_id' => $stock_transfer->id,
            'created_at' => $now,
            'updated_at' => $now,
          ];
        }

        $snIdsToRevert = $detail->serialNumbers()->pluck('serial_numbers.id')->toArray();

        if (!empty($snIdsToRevert)) {
          SerialNumber::whereIn('id', $snIdsToRevert)->update([
            'status' => 'Tersedia',
          ]);
        }

        // Batch Insert pergerakan stok
        if (!empty($movements)) {
          StockMovement::insert($movements);
        }

        $stock_transfer->update([
          'status' => StockTransfer::STATUS_DITOLAK,
          'tanggal_diterima' => $now,
          'user_terima_id' => $user->id,
          'catatan_terima' => $validated['catatan_terima'],
        ]);
      });

      return redirect()->route('stok-transfer.show', $stock_transfer->kode_transfer)->with('success', 'Transfer stok ditolak dan stok telah dikembalikan ke toko asal.');
    } catch (\Exception $e) {
      return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
  }

  /**
   * Query dasar transfer, di-scope berdasarkan akses store user
   * dan filter dari request (search, status, store asal/tujuan, tanggal).
   */
  private function buildQuery(Request $request)
  {
    $user = Auth::user();

    $query = StockTransfer::query()
      ->with(['storeAsal', 'storeTujuan', 'userKirim', 'userTerima'])
      ->withCount('details')
      ->latest('tanggal_kirim');

    // IDOR guard: staff non 'view-toko-gudang' cuma boleh liat transfer
    // yang melibatkan store dia sendiri (baik sebagai asal maupun tujuan).
    if (!$user->can('view-toko-gudang')) {
      $query->forStore($user->employee->store_id);
    } elseif ($request->filled('store_id')) {
      $query->forStore($request->store_id);
    }

    if ($request->filled('search')) {
      $search = $request->search;
      $query->where(function ($q) use ($search) {
        $q->where('kode_transfer', 'like', "%{$search}%")
          ->orWhereHas('userKirim', fn($qUser) => $qUser->where('username', 'like', "%{$search}%"))
          ->orWhereHas('userTerima', fn($qUser) => $qUser->where('username', 'like', "%{$search}%"));
      });
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('store_asal_id')) {
      $query->where('store_asal_id', $request->store_asal_id);
    }

    if ($request->filled('store_tujuan_id')) {
      $query->where('store_tujuan_id', $request->store_tujuan_id);
    }

    if ($request->filled('start_date') && $request->filled('end_date')) {
      $query->whereBetween('tanggal_kirim', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
    }

    return $query;
  }
}
