<?php

namespace Modules\Inventory\Http\Controllers\stok;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockOpname;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Category::where('status', 1)->whereHas('products')->orderBy('name')->get();
        
        // Ambil data semua toko/cabang untuk pilihan dropdown di form
        $tokos = Store::all(); // <-- Sesuaikan dengan Model Toko Anda
        
        $products = collect(); 
        $selectedKategori = $request->input('kategori');
        $selectedToko = $request->input('store_id'); // <-- Input Toko

        // WAJIB: Form lembar kerja hanya muncul jika Kategori DAN Toko sudah dipilih
        if ($request->filled('kategori') && $request->filled('store_id')) {
            
            // Eager loading relasi 'stocks' tapi di-filter HANYA untuk toko yang dipilih
            $query = Product::where('category_id', $selectedKategori)
                ->with(['category', 'stocks' => function($q) use ($selectedToko) {
                    $q->where('store_id', $selectedToko); 
                }]);

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name_product', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            }
            
            $products = $query->get(); 
        }

        return view('inventory::inventaris.opname.stok-opname', [
            'title' => 'Stock Opname',
            'products' => $products,
            'kategoris' => $kategoris,
            'tokos' => $tokos,
            'selectedKategori' => $selectedKategori,
            'selectedToko' => $selectedToko
        ]);
    }

    /**
     * Menyimpan hasil stok opname ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'store_id' => 'required|integer', // Wajib tahu lokasi tokonya
            'catatan_opname' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.stok_fisik' => 'required|integer|min:0',
            'items.*.stok_sistem_awal' => 'required|integer|min:0',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        $tokoId = $validatedData['store_id'];
        $itemsToProcess = [];
        $productIds = array_keys($validatedData['items']);
        
        // Muat produk berserta stok khusus toko ini saja
        $products = Product::with(['stocks' => function($q) use ($tokoId) {
            $q->where('store_id', $tokoId);
        }])->findMany($productIds)->keyBy('id');

        foreach ($validatedData['items'] as $produkId => $item) {
            $produk = $products->get($produkId);
            if ($produk) {
                // Karena query di atas sudah di-filter per store_id, isi collection 'stocks' maksimal hanya 1 row
                $stockRow = $produk->stocks->first();
                $stokSistem = $stockRow ? $stockRow->qty : 0; 

                // Proteksi Concurrency
                if ($stokSistem != $item['stok_sistem_awal']) {
                    return back()->withInput()->with('error', "Opname Gagal! Stok sistem untuk '{$produk->name_product}' di toko ini telah berubah. Seseorang melakukan transaksi saat Anda menghitung.");
                }

                $stokFisik = (int)$item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                if ($selisih != 0) {
                    $itemsToProcess[$produkId] = [
                        'produk' => $produk,
                        'stok_sistem' => $stokSistem,
                        'stok_fisik' => $stokFisik,
                        'selisih' => $selisih,
                        'keterangan' => $item['keterangan'],
                    ];
                }
            }
        }

        if (empty($itemsToProcess)) {
            return redirect()->route('stok-opname.index')->with('info', 'Tidak ada perubahan stok.');
        }

        try {
            DB::transaction(function () use ($itemsToProcess, $request, $tokoId) {
                $stokOpname = StockOpname::create([
                    'kode_opname' => $this->generateOpnameCode(),
                    'tanggal_opname' => now(),
                    'user_id' => Auth::id(),
                    'catatan' => $request->input('catatan_opname'),
                    'status' => 'Selesai',
                    // 'store_id' => $tokoId // Bagus jika tabel master opname Anda punya kolom lokasi toko
                ]);

                foreach ($itemsToProcess as $produkId => $data) {
                    $stokOpname->details()->create([
                        'product_id' => $produkId,
                        'stok_sistem' => $data['stok_sistem'],
                        'stok_fisik' => $data['stok_fisik'],
                        'selisih' => $data['selisih'],
                        'keterangan' => $data['keterangan'],
                    ]);

                    // UPDATE ATAU BUAT DATA STOK BARU KHUSUS DI TOKO INI
                    \Modules\Inventory\Models\ProductStock::updateOrCreate(
                        ['product_id' => $produkId, 'store_id' => $tokoId],
                        ['qty' => $data['stok_fisik']]
                    );
                }
            });

            return redirect()->route('stok-opname.index')->with('success', 'Stok toko berhasil disesuaikan!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    /**
     * Menghasilkan kode unik untuk setiap sesi stok opname.
     */
    private function generateOpnameCode()
    {
        $date = now()->format('Ymd');
        $prefix = 'SO-' . $date . '-';

        $lastOpname = StockOpname::where('kode_opname', 'like', $prefix . '%')->latest('kode_opname')->first();

        $sequence = 1;
        if ($lastOpname) {
            $lastSequence = (int) substr($lastOpname->kode_opname, -4);
            $sequence = $lastSequence + 1;
        }

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menampilkan riwayat stok opname.
     */
    public function history(Request $request)
    {
        $query = StockOpname::with('user')->latest();

        // Fitur pencarian berdasarkan kode opname atau username
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_opname', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('username', 'like', "%{$search}%");
                    });
            });
        }

        $stokOpnames = $query->paginate(15)->withQueryString();

        return view('inventory::inventaris.opname.stok-opname-history', [
            'title' => 'Riwayat Stock Opname',
            'stokOpnames' => $stokOpnames,
        ]);
    }

    /**
     * Menampilkan detail dari sebuah riwayat stok opname.
     *
     * @param StockOpname $stok_opname
     * @return \Illuminate\View\View
     */
    public function show($kode_opname)
    {
        // Cari stok opname berdasarkan kode unik, bukan ID.
        $stok_opname = StockOpname::where('kode_opname', $kode_opname)->firstOrFail();

        // Eager load relasi yang dibutuhkan untuk efisiensi query
        $stok_opname->load([
            'user', // Muat relasi user
            'details.produk.unit'
        ]);

        return view('inventory::inventaris.opname.stok-opname-show', [
            'title' => 'Detail Stock Opname ' . $stok_opname->kode_opname,
            'stokOpname' => $stok_opname,
        ]);
    }
}
