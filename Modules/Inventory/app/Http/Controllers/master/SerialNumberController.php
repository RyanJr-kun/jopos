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

    public function index(Request $request, $produk_slug = null) // Terima parameter slug
    {
        $query = SerialNumber::with(['produk', 'penjualan'])->latest();
        $produkDipilih = null; // Variabel untuk menampung produk yang dipilih via slug

        // Jika ada slug dari URL, cari produknya
        if ($produk_slug) {
            // PERBAIKAN: Gunakan withCount untuk efisiensi query saat menghitung SN di view.
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

    public function getProductInfo(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $produk = Product::find($request->product_id);
        $stokTercatat = $produk->stocks()->sum('qty');
        $snTerdaftar = SerialNumber::where('product_id', $request->product_id)->count();
        return response()->json([
            'stok_tercatat' => $stokTercatat,
            'sn_terdaftar' => $snTerdaftar
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
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'serial_numbers' => 'required|array|min:1',
            'serial_numbers.*' => [
                'required',
                'string',
                'distinct', // Ensures no duplicates in the submitted list
                // Ensures the serial number is unique for this specific product
                Rule::unique('serial_numbers', 'nomor_seri')->where(function ($query) use ($request) {
                    return $query->where('product_id', $request->product_id);
                }),
            ],
        ], [
            // Custom error messages
            'serial_numbers.*.distinct' => 'Nomor seri :input terduplikasi dalam daftar yang Anda kirim.',
            'serial_numbers.*.unique' => 'Nomor seri :input sudah terdaftar untuk produk ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity is a good choice for validation errors
        }

        try {
            $validated = $validator->validated();
            $serialsToInsert = [];
            $now = now();
            $storeId = Auth::user()->employee?->store_id;

            if (!$storeId) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke cabang/toko manapun.'], 403);
            }

            foreach ($validated['serial_numbers'] as $serial) {
                $serialsToInsert[] = [
                    'store_id'   => $storeId,
                    'product_id' => $validated['product_id'],
                    'nomor_seri' => $serial,
                    'status' => 'Tersedia', // Set default status
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Use bulk insert for better performance
            SerialNumber::insert($serialsToInsert);

            return response()->json([
                'message' => count($serialsToInsert) . ' nomor seri berhasil ditambahkan.'
            ], 201); // 201 Created is the correct status code for successful creation
        } catch (\Exception $e) {
            // Tangkap error database tak terduga
            return response()->json([
                'message' => 'Terjadi kesalahan pada server saat menyimpan data.',
                'error'   => $e->getMessage() // Sembunyikan pesan asli ini jika di tahap production
            ], 500);
        }
    }

    public function storeMultiple(Request $request)
    {
        // --- PERBAIKAN: Tambahkan Validasi ---
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'serial_numbers' => 'required|array|min:1',
            'serial_numbers.*' => [
                'required',
                'string',
                'distinct',
                Rule::unique('serial_numbers', 'nomor_seri')->where(function ($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            }),
            ],
        ], [
            'serial_numbers.*.required' => 'Nomor seri tidak boleh kosong.',
            'serial_numbers.*.distinct' => 'Terdapat nomor seri duplikat pada input Anda.',
            'serial_numbers.*.unique' => 'Nomor seri :input sudah terdaftar untuk produk ini.',
        ]);

        DB::beginTransaction();
        try {
            $productId = $validated['product_id'];
            $serialNumbers = $validated['serial_numbers'];
            $now = now();
            $dataToInsert = [];
            $storeId = Auth::user()->employee?->store_id;

            if (!$storeId) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke cabang/toko manapun.'], 403);
            }

            foreach ($serialNumbers as $sn) {
                $dataToInsert[] = [
                    'store_id'   => $storeId,
                    'product_id' => $productId,
                    'nomor_seri' => $sn,
                    'status' => 'Tersedia',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            SerialNumber::insert($dataToInsert);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($dataToInsert) . ' nomor seri berhasil ditambahkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat menyimpan multiple serial numbers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Mengambil informasi detail produk untuk halaman manajemen Serial Number.
     *
     * @param  string  $product_id  // Samakan namanya jadi $product_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductInfoForSerial(string $product_id)
    {
        $produk = Product::find($product_id);

        // Jika produk tidak ditemukan, kirim respons JSON yang jelas, bukan 404.
        if (!$produk) {
            return response()->json(['message' => 'Product tidak ditemukan.'], 404);
        }
 
        $snTerdaftar = $produk->serialNumbers()
            ->whereNotIn('status', ['Terjual', 'Hilang'])
            ->count();
        
        $totalQty = $produk->stocks()->sum('qty');

        return response()->json([ 
            'qty' => $totalQty,
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
                Rule::unique('serial_numbers', 'nomor_seri')->where('product_id', $serialNumber->product_id)->ignore($serialNumber->id),
            ],
            'status' => 'required|in:Tersedia,Rusak,Hilang',
        ]);

        // Memulai transaksi database untuk menjaga integritas data
        DB::beginTransaction();
        try {
            // Cukup update data nomor serinya saja
            $serialNumber->update([
                'nomor_seri' => $validated['serial_number'],
                'status'     => $validated['status'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data nomor seri berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update serial number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nomor seri.'
            ], 500);
        }
    }

    // --- PERBAIKAN: Isi method destroy ---
    public function destroy(SerialNumber $serialNumber)
    {
        // Tambahkan proteksi: jangan hapus SN yang sudah terjual
        if ($serialNumber->status == 'Terjual') {
            return response()->json([
                'success' => false,
                'message' => 'Nomor seri yang sudah terjual tidak dapat dihapus.'
            ], 422); // Unprocessable Entity
        }

        try {
            $serialNumber->delete();
            return response()->json([
                'success' => true,
                'message' => 'Nomor seri berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error delete serial number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus nomor seri.'
            ], 500);
        }
    }

    public function getByProduct(String $product_id)
    {
        // Pastikan ini adalah request AJAX untuk keamanan
        if (!request()->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }
        try {
            // PERBAIKAN: Cari produk secara manual berdasarkan ID
            $produk = Product::find($product_id);

            // Jika produk tidak ditemukan, kirim respons yang jelas, bukan 404
            if (!$produk) {
                return response()->json(['error' => 'Product tidak ditemukan.'], 404);
            }

            $storeId = Auth::user()->employee?->store_id;

            $serialNumbers = SerialNumber::where('product_id', $product_id)
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->get(['nomor_seri', 'status']);

            return response()->json(['serial_numbers' => $serialNumbers->map(fn($sn) => [
                    'serial_number' => $sn->nomor_seri,
                    'status'        => $sn->status,
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data nomor seri.'], 500);
        }
    }
}
