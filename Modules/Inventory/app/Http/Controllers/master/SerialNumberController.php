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

        return view(
            'inventory::inventaris.sn.serial-number',
            compact('serialNumbers', 'products', 'produkDipilih', 'status'),
        );
    }

    public function getProductInfo(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $produk = Product::find($request->product_id);
        $stokTercatat = $produk->stocks()->sum('qty');
        $snTerdaftar = SerialNumber::where('product_id', $request->product_id)->count();
        return response()->json([
            'stok_tercatat' => $stokTercatat,
            'sn_terdaftar' => $snTerdaftar,
        ]);
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
            $variantBelongsToProduct = ProductVariant::where('id', $request->product_variant_id)
                ->where('product_id', $request->product_id)
                ->exists();

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

    /**
     * Mengambil informasi detail produk untuk halaman manajemen Serial Number.
     *
     * @param  string  $product_id  // Samakan namanya jadi $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductInfoForSerial(Request $request, string $product_id)
    {
        $produk = Product::find($product_id);
        $variantId = $request->product_variant_id;

        if (!$produk) {
            return response()->json(['message' => 'Product tidak ditemukan.'], 404);
        }

        $variant_id = $request->query('variant_id');

        $snTerdaftar = SerialNumber::where('product_id', $request->product_id)
            ->when(
                $variantId,
                function ($query, $variantId) {
                    // Jika ada variant_id, cari yang sesuai
                    return $query->where('product_variant_id', $variantId);
                },
                function ($query) {
                    // Jika tidak ada variant_id (produk simpel), cari yang null
                    return $query->whereNull('product_variant_id');
                },
            )
            ->whereNotIn('status', ['Terjual', 'Hilang']) // 👈 JANGAN LUPA INI
            ->count();

        // 2. Hitung Total Stok KHUSUS UNTUK VARIAN INI
        $storeId = Auth::user()->employee?->store_id;
        $stockQuery = ProductStock::where('product_id', $product_id);

        if ($variant_id) {
            $stockQuery->where('product_variant_id', $variant_id);
        } else {
            $stockQuery->whereNull('product_variant_id');
        }

        if ($storeId) {
            $stockQuery->where('store_id', $storeId);
        }

        $totalQty = $stockQuery->sum('qty');

        return response()->json([
            'qty' => (int) $totalQty,
            'sn_tercatat_count' => $snTerdaftar,
            'butuh_sn' => max(0, $totalQty - $snTerdaftar),
        ]);
    }

    // --- PERBAIKAN: Isi method update ---
    public function update(Request $request, SerialNumber $serialNumber)
    {
        $validated = $request->validate([
            'serial_number' => [
                'required',
                Rule::unique('serial_numbers', 'nomor_seri')
                    ->where('product_id', $serialNumber->product_id)
                    ->ignore($serialNumber->id),
            ],
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

    public function getProduct(Request $request, string $product_id)
    {
        // Pastikan ini adalah request AJAX untuk keamanan
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        try {
            $produk = Product::find($product_id);

            if (!$produk) {
                return response()->json(['error' => 'Product tidak ditemukan.'], 404);
            }

            $storeId = Auth::user()->employee?->store_id;
            $search = $request->query('search');
            $variantId = $request->query('variant_id'); // Amkap dari AJAX jika produk bervarian

            // Bangun Query Nomor Seri
            $query = SerialNumber::where('product_id', $product_id)
                ->where('status', 'Tersedia') // PENTING: Hanya tampilkan SN yang bisa dijual
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->when(
                    $variantId,
                    function ($q) use ($variantId) {
                        // Jika ada variant_id, cari SN khusus varian tersebut
                        return $q->where('product_variant_id', $variantId);
                    },
                    function ($q) {
                        // Jika tidak ada varian, pastikan mencari SN produk utama (null)
                        return $q->whereNull('product_variant_id');
                    },
                )
                ->when($search, function ($q, $search) {
                    // Filter pencarian jika kasir mengetik nomor seri
                    return $q->where('nomor_seri', 'like', "%{$search}%");
                })
                ->latest(); // Urutkan dari yang terbaru

            // Gunakan paginate agar mirip dengan getData
            $serialNumbers = $query->paginate(15);

            // Format data agar sesuai dengan standar Select2
            $formattedData = [];
            foreach ($serialNumbers as $sn) {
                $formattedData[] = [
                    'id' => $sn->nomor_seri, // Value yang akan disubmit (nomor serinya)
                    'text' => $sn->nomor_seri, // Teks yang tampil di dropdown Select2
                    'status' => $sn->status,
                ];
            }

            return response()->json([
                'data' => $formattedData,
                'current_page' => $serialNumbers->currentPage(),
                'next_page_url' => $serialNumbers->nextPageUrl(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getProduct SN: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengambil data nomor seri.'], 500);
        }
    }
}
