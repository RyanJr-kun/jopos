<?php

namespace Modules\Inventory\Http\Controllers\stok;

use App\Http\Controllers\Controller;

use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\StockTake;
use Illuminate\Http\Request;
use Modules\Inventory\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockTakeController extends Controller
{
    /**
     * Menampilkan halaman stok opname.
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->latest();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name_product', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        if ($request->filled('kategori')) {
            $query->where('category_id', $request->input('kategori'));
        }
        $products = $query->paginate(50)->withQueryString();
        $kategoris = Category::where('status', 1)->whereHas('products')->orderBy('name')->get();

        return view('inventory::inventaris.stok-opname', [
            'title' => 'Stock Opname',
            'products' => $products,
            'kategoris' => $kategoris,
        ]);
    }

    /**
     * Menyimpan hasil stok opname ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input dari form
        $validatedData = $request->validate([
            'catatan_opname' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.stok_fisik' => 'required|integer|min:0',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        // 2. Filter hanya item yang memiliki selisih stok
        $itemsToProcess = [];
        $productIds = array_keys($validatedData['items']);
        // Ambil semua produk yang relevan dalam satu query untuk efisiensi
        $products = Product::findMany($productIds)->keyBy('id');

        foreach ($validatedData['items'] as $produkId => $item) {
            $produk = $products->get($produkId);
            if ($produk) {
                $stokSistem = $produk->qty;
                $stokFisik = (int)$item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                // Hanya proses item yang stoknya benar-benar berubah
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

        // Jika tidak ada item yang berubah, kembali dengan pesan info
        if (empty($itemsToProcess)) {
            return redirect()->route('stok-opname.index')->with('info', 'Tidak ada perubahan stok yang perlu disimpan.');
        }

        // 3. Gunakan Database Transaction untuk memastikan integritas data
        try {
            DB::transaction(function () use ($itemsToProcess, $request) {
                // Buat record master StockTake
                $stokOpname = StockTake::create([
                    'kode_opname' => $this->generateOpnameCode(),
                    'tanggal_opname' => now(),
                    'user_id' => Auth::id(),
                    'catatan' => $request->input('catatan_opname'),
                    'status' => 'Selesai',
                ]);

                // Loop dan proses setiap item yang memiliki selisih
                foreach ($itemsToProcess as $produkId => $data) {
                    // Buat record detail untuk riwayat
                    $stokOpname->details()->create([
                        'product_id' => $produkId,
                        'stok_sistem' => $data['stok_sistem'],
                        'stok_fisik' => $data['stok_fisik'],
                        'selisih' => $data['selisih'],
                        'keterangan' => $data['keterangan'],
                    ]);

                    // Update kuantitas (stok) di tabel produk
                    $data['produk']->update(['qty' => $data['stok_fisik']]);
                }
            });

            return redirect()->route('stok-opname.index')->with('success', 'Hasil stok opname berhasil disimpan dan stok produk telah diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Menghasilkan kode unik untuk setiap sesi stok opname.
     */
    private function generateOpnameCode()
    {
        $date = now()->format('Ymd');
        $prefix = 'SO-' . $date . '-';

        $lastOpname = StockTake::where('kode_opname', 'like', $prefix . '%')->latest('kode_opname')->first();

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
        $query = StockTake::with('user')->latest();

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

        return view('inventory::inventaris.stok-opname-history', [
            'title' => 'Riwayat Stock Opname',
            'stokOpnames' => $stokOpnames,
        ]);
    }

    /**
     * Menampilkan detail dari sebuah riwayat stok opname.
     *
     * @param StockTake $stok_opname
     * @return \Illuminate\View\View
     */
    public function show($kode_opname)
    {
        // Cari stok opname berdasarkan kode unik, bukan ID.
        $stok_opname = StockTake::where('kode_opname', $kode_opname)->firstOrFail();

        // Eager load relasi yang dibutuhkan untuk efisiensi query
        $stok_opname->load([
            'user', // Muat relasi user
            'details.produk.unit'
        ]);

        return view('inventory::inventaris.stok-opname-show', [
            'title' => 'Detail Stock Opname ' . $stok_opname->kode_opname,
            'stokOpname' => $stok_opname,
        ]);
    }
}
