<?php

namespace App\Http\Controllers\master;

use App\Http\Controllers\Controller;

use App\Models\Produk;
use App\Models\SerialNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // <-- Tambahkan ini

class SerialNumberController extends Controller
{
    public function index(Request $request, $produk_slug = null) // Terima parameter slug
    {
        $query = SerialNumber::with(['produk', 'penjualan'])->latest();
        $produkDipilih = null; // Variabel untuk menampung produk yang dipilih via slug

        // Jika ada slug dari URL, cari produknya
        if ($produk_slug) {
            // PERBAIKAN: Gunakan withCount untuk efisiensi query saat menghitung SN di view.
            $produkDipilih = Produk::withCount('serialNumbers')->where('slug', $produk_slug)->first();
            // Jika produk ditemukan, langsung filter daftar SN untuk produk tersebut
            if ($produkDipilih) {
                $query->where('produk_id', $produkDipilih->id);
            }
        }

        // Filter lainnya tetap berfungsi seperti biasa
        if ($request->filled('search')) {
            $query->where('nomor_seri', 'like', '%' . $request->search . '%');
        }

        // Filter produk dari dropdown akan menimpa filter dari slug jika digunakan
        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $serialNumbers = $query->paginate(15)->withQueryString();
        $produks = Produk::where('wajib_seri', true)->orderBy('nama_produk')->get();

        return view('dashboard.inventaris.serial-number', [
            'title' => 'Manajemen Nomor Seri',
            'serialNumbers' => $serialNumbers,
            'produks' => $produks,
            'produkDipilih' => $produkDipilih, // Kirim produk yang dipilih ke view
        ]);
    }

    public function getProductInfo(Request $request)
    {
        $request->validate(['produk_id' => 'required|exists:produks,id']);
        $produk = Produk::find($request->produk_id);
        $stokTercatat = $produk->qty;
        $snTerdaftar = SerialNumber::where('produk_id', $request->produk_id)->count();
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
            'produk_id' => 'required|exists:produks,id',
            'serial_numbers' => 'required|array|min:1',
            'serial_numbers.*' => [
                'required',
                'string',
                'distinct', // Ensures no duplicates in the submitted list
                // Ensures the serial number is unique for this specific product
                Rule::unique('serial_numbers', 'nomor_seri')->where(function ($query) use ($request) {
                    return $query->where('produk_id', $request->produk_id);
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

        $validated = $validator->validated();
        $serialsToInsert = [];
        $now = now();

        foreach ($validated['serial_numbers'] as $serial) {
            $serialsToInsert[] = [
                'produk_id' => $validated['produk_id'],
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
    }

    public function storeMultiple(Request $request)
    {
        // --- PERBAIKAN: Tambahkan Validasi ---
        $validated = $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'serial_numbers' => 'required|array|min:1',
            // Pastikan setiap nomor seri unik di tabel untuk produk_id yang sama
            'serial_numbers.*' => [
                'required',
                'distinct',
                Rule::unique('serial_numbers', 'nomor_seri')->where('produk_id', $request->produk_id),
            ],
        ], [
            'serial_numbers.*.required' => 'Nomor seri tidak boleh kosong.',
            'serial_numbers.*.distinct' => 'Terdapat nomor seri duplikat pada input Anda.',
            'serial_numbers.*.unique' => 'Nomor seri :input sudah terdaftar untuk produk ini.',
        ]);

        DB::beginTransaction();
        try {
            $productId = $validated['produk_id'];
            $serialNumbers = $validated['serial_numbers'];
            $now = now();
            $dataToInsert = [];

            foreach ($serialNumbers as $sn) {
                $dataToInsert[] = [
                    'produk_id' => $productId,
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
     * Didesain untuk dipanggil via AJAX.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductInfoForSerial($produk_id)
    {
        $produk = Produk::find($produk_id);

        // Jika produk tidak ditemukan, kirim respons JSON yang jelas, bukan 404.
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan.'], 404);
        }

        // Hitung SN yang masih dianggap sebagai aset (bukan terjual atau hilang)
        $snTerdaftar = $produk->serialNumbers()
            ->whereNotIn('status', ['Terjual', 'Hilang'])
            ->count();

        return response()->json([
            'qty' => $produk->qty,
            'sn_tercatat_count' => $snTerdaftar,
            'butuh_sn' => max(0, $produk->qty - $snTerdaftar), // Pastikan tidak mengembalikan angka negatif
        ]);
    }

    // --- PERBAIKAN: Isi method update ---
    public function update(Request $request, SerialNumber $serialNumber)
    {
        $validated = $request->validate([
            'serial_number' => [
                'required',
                // Pastikan unik untuk produk yang sama, kecuali untuk dirinya sendiri
                Rule::unique('serial_numbers', 'nomor_seri')->where('produk_id', $serialNumber->produk_id)->ignore($serialNumber->id),
            ],
            // Batasi status yang bisa diubah. 'Terjual' tidak bisa diubah dari sini
            // karena seharusnya diatur oleh transaksi penjualan.
            'status' => 'required|in:Tersedia,Rusak,Hilang',
        ]);

        // Memulai transaksi database untuk menjaga integritas data
        DB::beginTransaction();
        try {
            // 1. Simpan status lama sebelum ada perubahan
            $old_status = $serialNumber->status;
            $new_status = $validated['status'];

            // 2. Update data nomor seri
            $serialNumber->update([
                'nomor_seri' => $validated['serial_number'],
                'status' => $new_status,
            ]);

            // 3. Logika penyesuaian stok produk
            // Jika status berubah MENJADI 'Hilang' atau 'Rusak' (dari status apapun yang bukan itu)
            if (($new_status === 'Hilang' || $new_status === 'Rusak') && ($old_status !== 'Hilang' && $old_status !== 'Rusak')) {
                $serialNumber->produk->decrement('qty');
            }
            // Jika status berubah DARI 'Hilang' atau 'Rusak' MENJADI 'Tersedia' (misal: barang ditemukan kembali)
            else if (($old_status === 'Hilang' || $old_status === 'Rusak') && $new_status === 'Tersedia') {
                $serialNumber->produk->increment('qty');
            }
            // Tidak ada perubahan stok jika status tidak berubah, atau jika perubahannya antara Rusak dan Hilang.

            // Jika semua berhasil, commit transaksi
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Nomor seri berhasil diperbarui dan stok telah disesuaikan.'
            ]);
        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua perubahan
            DB::rollBack();

            Log::error('Error update serial number: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui nomor seri. Terjadi kesalahan pada server.'
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

    public function getByProduct($produk_id)
    {
        // Pastikan ini adalah request AJAX untuk keamanan
        if (!request()->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }
        try {
            // PERBAIKAN: Cari produk secara manual berdasarkan ID
            $produk = Produk::find($produk_id);

            // Jika produk tidak ditemukan, kirim respons yang jelas, bukan 404
            if (!$produk) {
                return response()->json(['error' => 'Produk tidak ditemukan.'], 404);
            }

            $serialNumbers = SerialNumber::where('produk_id', $produk_id)
                ->where('status', 'Tersedia')
                ->pluck('nomor_seri'); // Ambil hanya kolom nomor_seri

            return response()->json($serialNumbers);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data nomor seri.'], 500);
        }
    }
}
