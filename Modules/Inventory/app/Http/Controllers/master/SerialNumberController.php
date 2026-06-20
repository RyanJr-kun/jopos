<?php

namespace Modules\Inventory\Http\Controllers\master;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\ProductVariant;
use Modules\Inventory\Models\SerialNumber;

class SerialNumberController extends Controller implements HasMiddleware
{
  public static function middleware(): array
  {
    return [
      new Middleware('permission:view-serialnumber', only: ['index', 'getByProduct']),
      new Middleware('permission:create-serialnumber', only: ['create', 'store']),
      new Middleware('permission:edit-serialnumber', only: ['edit', 'update']),
      new Middleware('permission:delete-serialnumber', only: ['destroy']),
    ];
  }

  public function index(Request $request, $produk_slug = null)
  {
    // Terima parameter slug
    $query = SerialNumber::with(['produk.primaryImage', 'variant', 'penjualan'])->latest();
    $produkDipilih = null; // Variabel untuk menampung produk yang dipilih via slug

    // Jika ada slug dari URL, cari produknya
    if ($produk_slug) {
      $produkDipilih = Product::withCount('serialNumbers')->where('slug', $produk_slug)->first();
      // Jika produk ditemukan, langsung filter daftar SN untuk produk tersebut
      if ($produkDipilih) {
        $query->where('product_id', $produkDipilih->id);
      }
    }

    // Filter lainnya tetap berfungsi seperti biasa
    if ($request->filled('search')) {
      $query->where('nomor_seri', 'like', '%' . $request->search . '%');
    }

    // Filter produk dari dropdown akan menimpa filter dari slug jika digunakan
    if ($request->filled('product_id')) {
      $query->where('product_id', $request->product_id);
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    $serialNumbers = $query->paginate(15)->withQueryString();
    $products = Product::where('wajib_seri', true)->orderBy('name_product')->get();
    $status = SerialNumber::getStatus();

    return view('inventory::inventaris.sn.serial-number', compact('serialNumbers', 'products', 'produkDipilih', 'status'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(Request $request)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'product_id' => 'required|exists:products,id',
        'product_variant_id' => 'nullable|exists:product_variants,id',
        'serial_numbers' => 'required|array|min:1',
        'serial_numbers.*' => [
          'required',
          'string',
          'distinct',
          // Unique per produk (bukan per varian) — sesuai unique constraint di schema
          Rule::unique('serial_numbers', 'nomor_seri')->where(function ($query) use ($request) {
            return $query->where('product_id', $request->product_id);
          }),
        ],
      ],
      [
        'product_variant_id.exists' => 'Varian produk tidak ditemukan.',
        'serial_numbers.*.distinct' => 'Nomor seri :input terduplikasi dalam daftar yang Anda kirim.',
        'serial_numbers.*.unique' => 'Nomor seri :input sudah terdaftar untuk produk ini.',
      ],
    );

    if ($validator->fails()) {
      return response()->json(
        [
          'message' => 'Data yang diberikan tidak valid.',
          'errors' => $validator->errors(),
        ],
        422,
      );
    }

    // Jika ada variant_id, pastikan variant tersebut memang milik product_id yang dikirim
    if ($request->filled('product_variant_id')) {
      $variantBelongsToProduct = ProductVariant::where('id', $request->product_variant_id)->where('product_id', $request->product_id)->exists();

      if (!$variantBelongsToProduct) {
        return response()->json(
          [
            'message' => 'Varian tidak sesuai dengan produk yang dipilih.',
          ],
          422,
        );
      }
    }

    try {
      $validated = $validator->validated();
      $now = now();
      $storeId = Auth::user()->employee?->store_id;
      $variantId = $request->input('product_variant_id') ?: null;

      if (!$storeId) {
        return response()->json(
          [
            'message' => 'Anda tidak memiliki akses ke cabang/toko manapun.',
          ],
          403,
        );
      }

      $serialsToInsert = [];
      foreach ($validated['serial_numbers'] as $serial) {
        $serialsToInsert[] = [
          'store_id' => $storeId,
          'product_id' => $validated['product_id'],
          'product_variant_id' => $variantId,
          'nomor_seri' => $serial,
          'status' => 'Tersedia',
          'created_at' => $now,
          'updated_at' => $now,
        ];
      }

      SerialNumber::insert($serialsToInsert);

      return response()->json(
        [
          'message' => count($serialsToInsert) . ' nomor seri berhasil ditambahkan.',
        ],
        201,
      );
    } catch (\Exception $e) {
      Log::error('Error saat menyimpan serial numbers: ' . $e->getMessage());
      return response()->json(
        [
          'message' => 'Terjadi kesalahan pada server saat menyimpan data.',
        ],
        500,
      );
    }
  }

  // --- PERBAIKAN: Isi method update ---
  public function update(Request $request, SerialNumber $serialNumber)
  {
    $validated = $request->validate([
      'serial_number' => ['required', Rule::unique('serial_numbers', 'nomor_seri')->where('product_id', $serialNumber->product_id)->ignore($serialNumber->id)],
      'status' => 'required|in:Tersedia,Rusak,Hilang',
    ]);

    // Memulai transaksi database untuk menjaga integritas data
    DB::beginTransaction();
    try {
      // Cukup update data nomor serinya saja
      $serialNumber->update([
        'nomor_seri' => $validated['serial_number'],
        'status' => $validated['status'],
      ]);

      DB::commit();

      return response()->json([
        'success' => true,
        'message' => 'Data nomor seri berhasil diperbarui.',
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error update serial number: ' . $e->getMessage());
      return response()->json(
        [
          'success' => false,
          'message' => 'Gagal memperbarui nomor seri.',
        ],
        500,
      );
    }
  }

  // --- PERBAIKAN: Isi method destroy ---
  public function destroy(SerialNumber $serialNumber)
  {
    // Tambahkan proteksi: jangan hapus SN yang sudah terjual
    if ($serialNumber->status == 'Terjual') {
      return response()->json(
        [
          'success' => false,
          'message' => 'Nomor seri yang sudah terjual tidak dapat dihapus.',
        ],
        422,
      ); // Unprocessable Entity
    }

    try {
      $serialNumber->delete();
      return response()->json([
        'success' => true,
        'message' => 'Nomor seri berhasil dihapus.',
      ]);
    } catch (\Exception $e) {
      Log::error('Error delete serial number: ' . $e->getMessage());
      return response()->json(
        [
          'success' => false,
          'message' => 'Gagal menghapus nomor seri.',
        ],
        500,
      );
    }
  }

  /**
   * Mengambil informasi detail produk untuk halaman manajemen Serial Number.
   *
   * @param  string  $product_id  // Samakan namanya jadi $product_id
   * @return \Illuminate\Http\JsonResponse
   */
  public function getProductInfoForSerial(Request $request, string $product_id)
  {
    $produk = Product::find($product_id);

    if (!$produk) {
      return response()->json(['message' => 'Product tidak ditemukan.'], 404);
    }

    $variantId = $request->query('variant_id');
    $storeId = Auth::user()->employee?->store_id;

    $snTerdaftar = SerialNumber::where('product_id', $product_id) // ← pakai route param
      ->when(
        $variantId,
        fn($q) => $q->where('product_variant_id', $variantId),
        fn($q) => $q->whereNull('product_variant_id'), // produk simple
      )
      ->when($storeId, fn($q) => $q->where('store_id', $storeId)) // filter per toko
      ->whereNotIn('status', ['Terjual', 'Hilang'])
      ->count();

    $totalQty = ProductStock::where('product_id', $product_id)
      ->when(
        $variantId,
        fn($q) => $q->where('product_variant_id', $variantId),
        fn($q) => $q->whereNull('product_variant_id'), // ← WAJIB ada ini
      )
      ->when($storeId, fn($q) => $q->where('store_id', $storeId))
      ->sum('qty');

    return response()->json([
      'qty' => (int) $totalQty,
      'sn_tercatat_count' => $snTerdaftar,
      'butuh_sn' => max(0, (int) $totalQty - $snTerdaftar),
    ]);
  }

  public function getProduct(Request $request)
  {
    if (!$request->ajax()) {
      return response()->json(['error' => 'Invalid request'], 400);
    }

    $productId = $request->query('product_id');
    $variantId = $request->query('variant_id'); // null = produk simple
    $storeId = Auth::user()->employee?->store_id;
    $search = $request->query('search');

    if (!$productId) {
      return response()->json(['error' => 'product_id wajib diisi.'], 422);
    }

    $produk = Product::find($productId);
    if (!$produk) {
      return response()->json(['error' => 'Product tidak ditemukan.'], 404);
    }

    $serialNumbers = SerialNumber::where('product_id', $productId)
      ->where('status', 'Tersedia')
      ->when($storeId, fn($q) => $q->where('store_id', $storeId))
      ->when(
        $variantId,
        fn($q) => $q->where('product_variant_id', $variantId),
        fn($q) => $q->whereNull('product_variant_id'), // produk simple
      )
      ->when($search, fn($q) => $q->where('nomor_seri', 'like', "%{$search}%"))
      ->latest()
      ->paginate(15);

    return response()->json([
      'serial_numbers' => $serialNumbers
        ->map(
          fn($sn) => [
            'serial_number' => $sn->nomor_seri,
            'status' => $sn->status,
          ],
        )
        ->values(),
      'current_page' => $serialNumbers->currentPage(),
      'next_page_url' => $serialNumbers->nextPageUrl(),
    ]);
  }
}
