<?php

namespace Modules\Inventory\Http\Controllers\stok;

use App\Http\Controllers\Controller;
use App\Models\ProductStock;
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
       $kategoris = Category::whereNull('parent_id')->with('children')->get();

        $tokos = Store::all();

        $products = collect();
        $selectedKategori = $request->input('kategori');       // ID kategori utama
        $selectedSubKategori = $request->input('subkategori');  // ID subkategori
        $selectedToko = $request->input('store_id');

        // WAJIB: Form lembar kerja hanya muncul jika Kategori Utama DAN Toko sudah dipilih
        if ($request->filled('kategori') && $request->filled('store_id')) {

            $query = Product::with(['category','primaryImage', 
            'stocks' => function ($q) use ($selectedToko) { // Stok produk tunggal
                $q->where('store_id', $selectedToko);
            },
            'variants.stocks' => function ($q) use ($selectedToko) { // Stok per varian
                $q->where('store_id', $selectedToko);
            }]);

            if ($request->filled('subkategori')) {
                // Subkategori dipilih -> hanya produk milik subkategori itu
                $query->where('category_id', $selectedSubKategori);
            } else {
                // Hanya kategori utama dipilih -> semua produk di subkategori-subkategorinya
                // (+ produk yang langsung menempel di kategori utama, kalau ada)
                $childIds = Category::where('parent_id', $selectedKategori)
                    ->pluck('id')
                    ->push($selectedKategori);

                $query->whereIn('category_id', $childIds);
            }

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
            'selectedSubKategori' => $selectedSubKategori,
            'selectedToko' => $selectedToko,
        ]);
    }

    /**
     * Menyimpan hasil stok opname ke database.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'store_id' => 'required|integer',
            'catatan_opname' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.product_variant_id' => 'nullable|integer', // Izinkan kosong jika produk tunggal
            'items.*.stok_fisik' => 'required|integer|min:0',
            'items.*.stok_sistem_awal' => 'required|integer|min:0',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        $tokoId = $validatedData['store_id'];
        $itemsToProcess = [];

        // Loop data dari form dan cek perlindungan concurrency langsung ke database
        foreach ($validatedData['items'] as $item) {
            // Query untuk mencari stok saat ini berdasarkan toko, produk, dan varian (jika ada)
            $stockQuery = ProductStock::where('store_id', $tokoId)
                ->where('product_id', $item['product_id']);
            
            if (!empty($item['product_variant_id'])) {
                $stockQuery->where('product_variant_id', $item['product_variant_id']);
            } else {
                $stockQuery->whereNull('product_variant_id'); // Pastikan produk tunggal tidak tertukar dengan varian
            }
            
            $stockRow = $stockQuery->first();
            $stokSistem = $stockRow ? $stockRow->qty : 0; 

            // Proteksi Concurrency
            if ($stokSistem != $item['stok_sistem_awal']) {
                return back()->withInput()->with('error', "Opname Gagal! Ada perubahan stok sistem pada salah satu barang saat Anda menghitung. Seseorang baru saja melakukan transaksi.");
            }

            $stokFisik = (int)$item['stok_fisik'];
            $selisih = $stokFisik - $stokSistem;

            if ($selisih != 0) {
                $itemsToProcess[] = [
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'] ?? null,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'keterangan' => $item['keterangan'],
                ];
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
                    'store_id' => $tokoId,
                ]);

                foreach ($itemsToProcess as $data) {
                    // 1. Catat ke history (tabel detail opname)
                    $stokOpname->details()->create([
                        'product_id' => $data['product_id'],
                        'product_variant_id' => $data['product_variant_id'], // Tambahkan ini di DB Anda
                        'stok_sistem' => $data['stok_sistem'],
                        'stok_fisik' => $data['stok_fisik'],
                        'selisih' => $data['selisih'],
                        'keterangan' => $data['keterangan'],
                    ]);

                    // 2. Sesuaikan Stok Fisik
                    ProductStock::updateOrCreate(
                        [
                            'product_id' => $data['product_id'], 
                            'store_id' => $tokoId,
                            'product_variant_id' => $data['product_variant_id']
                        ],
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
    $stok_opname = StockOpname::where('kode_opname', $kode_opname)->firstOrFail();

    $stok_opname->load([
        'user',
        'store', 
        'details.produk.unit',
        'details.produk.primaryImage',
        'details.produk.variants',
    ]);

    return view('inventory::inventaris.opname.stok-opname-show', [
        'title' => 'Detail Stock Opname ' . $stok_opname->kode_opname,
        'stokOpname' => $stok_opname,
    ]);
}
}
