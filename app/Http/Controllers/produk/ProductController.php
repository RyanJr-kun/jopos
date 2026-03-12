<?php

namespace App\Http\Controllers\produk;

use App\Http\Controllers\Controller;

use App\Models\Unit;
use App\Models\Brand;
use App\Models\Taxe;
use App\Models\Product;
use App\Models\Warrantie;
use Illuminate\Http\Request;
use App\Models\SaleItem;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil request
        $request = request();

        // Mulai query builder
        $query = Product::with(['category', 'brand', 'unit', 'user'])->latest();

        // Terapkan filter pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name_produk', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Terapkan filter kategori jika ada input 'kategori'
        if ($request->filled('kategori')) {
            $query->where('category_id', $request->input('kategori'));
        }

        $products = $query->paginate(10)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('content.produk.produk._produk_table', ['produk' => $products])->render();
        }

        return view('content.produk.produk.index', [
            'produk' => $products,
            'kategoris' => Category::where('status', 1)->whereHas('products')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('content.produk.produk.create', [
            'kategori' => Category::where('status', 1)->orderBy('name')->get(),
            'brand' => Brand::where('status', 1)->orderBy('name')->get(),
            'unit' => Unit::where('status', 1)->orderBy('name')->get(),
            'garansi' => Warrantie::where('status', 1)->orderBy('name')->get(),
            'pajak' => Taxe::orderBy('name_taxe')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name_produk' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug',
            'barcode' => 'nullable|string|unique:products,barcode',
            'sku' => 'required|string|unique:products,sku',
            'kategori' => 'required|exists:categories,id',
            'brand' => 'required|exists:brands,id',
            'unit' => 'required|exists:units,id',
            'description' => 'nullable|string',
            'harga_jual' => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'qty' => 'required|integer',
            'garansi' => 'required|exists:warranties,id',
            'stok_minimum' => 'required|integer',
            'pajak' => 'required|exists:taxes,id',
            'img_produk' => 'nullable|string',
            'wajib_seri' => 'nullable|boolean',
        ]);

        $validatedData['user_id'] = Auth::id();
        $validatedData['category_id'] = $validatedData['kategori'];
        $validatedData['brand_id'] = $validatedData['brand'];
        $validatedData['unit_id'] = $validatedData['unit'];
        $validatedData['taxe_id'] = $validatedData['pajak'];
        $validatedData['warrantie_id'] = $validatedData['garansi'];
        $validatedData['wajib_seri'] = $request->boolean('wajib_seri');

        // Pindahkan gambar dari temp ke folder produk
        if ($request->filled('img_produk') && str_starts_with($request->img_produk, 'tmp/')) {
            $tempPath = $request->input('img_produk');
            if (Storage::disk('public')->exists($tempPath)) {
                $newPath = 'produk/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $validatedData['img_produk'] = $newPath;
            }
        }

        unset($validatedData['kategori'], $validatedData['brand'], $validatedData['unit'], $validatedData['garansi'], $validatedData['pajak']);

        Product::create($validatedData);
        Alert::success('Berhasil', 'Product Baru Berhasil Ditambahkan.');
        return redirect()->route('produk.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $produk)
    {
        return view('content.produk.produk.show', [
            'produk' => $produk->load(['category', 'brand', 'unit', 'garansi', 'user', 'pajak'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $produk)
    {
        return view('content.produk.produk.edit', [
            'produk' => $produk,
            'kategoris' => Category::where('status', 1)->orderBy('name')->get(),
            'brands' => Brand::where('status', 1)->orderBy('name')->get(),
            'units' => Unit::where('status', 1)->orderBy('name')->get(),
            'warranties' => Warrantie::where('status', 1)->orderBy('name')->get(),
            'pajak' => Taxe::orderBy('name_taxe')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $produk)
    {
        $rules = [
            'name_produk' => 'required|string|max:255',
            'kategori' => 'required|exists:categories,id',
            'brand' => 'required|exists:brands,id',
            'unit' => 'required|exists:units,id',
            'pajak' => 'required|exists:taxes,id',
            'description' => 'nullable|string',
            'harga_jual' => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'qty' => 'required|integer',
            'garansi' => 'nullable|exists:warranties,id',
            'stok_minimum' => 'required|integer',
            'img_produk' => 'nullable|string',
            'slug' => ['required', 'string', Rule::unique('products', 'slug')->ignore($produk->id)],
            'barcode' => ['nullable', 'string', Rule::unique('products', 'barcode')->ignore($produk->id)],
            'sku' => ['required', 'string', Rule::unique('products', 'sku')->ignore($produk->id)],
            'wajib_seri' => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['user_id'] = Auth::id();
        $validatedData['category_id'] = $validatedData['kategori'];
        $validatedData['brand_id'] = $validatedData['brand'];
        $validatedData['unit_id'] = $validatedData['unit'];
        $validatedData['warrantie_id'] = $validatedData['garansi'];
        $validatedData['taxe_id'] = $validatedData['pajak'];
        $validatedData['wajib_seri'] = $request->boolean('wajib_seri');

        // Cek apakah ada gambar baru yang diunggah (path dimulai dengan 'tmp/')
        if ($request->filled('img_produk') && str_starts_with($request->img_produk, 'tmp/')) {
            $tempPath = $request->img_produk;
            if (Storage::disk('public')->exists($tempPath)) {
                // Hapus gambar lama jika ada
                if ($produk->img_produk && Storage::disk('public')->exists($produk->img_produk)) {
                    Storage::disk('public')->delete($produk->img_produk);
                }
                // Pindahkan gambar baru dari tmp ke folder produk (lebih aman dengan basename)
                $newPath = 'produk/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $newPath);
                $validatedData['img_produk'] = $newPath;
            }
            // Cek jika pengguna menghapus gambar (input ada tapi nilainya kosong/null)
        } elseif ($request->exists('img_produk') && $request->input('img_produk') === null) {
            if ($produk->img_produk && Storage::disk('public')->exists($produk->img_produk)) {
                Storage::disk('public')->delete($produk->img_produk);
                $validatedData['img_produk'] = null;
            }
        }

        unset($validatedData['kategori'], $validatedData['brand'], $validatedData['unit'], $validatedData['garansi'], $validatedData['pajak']);

        $produk->update($validatedData);
        Alert::success('Berhasil', 'Data Product Berhasil Diperbarui.');
        return redirect()->route('produk.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $produk)
    {
        try {
            // Pengecekan relasi sebelum menghapus
            if ($produk->itemSales()->exists() || $produk->pembelianDetails()->exists()) {
                $message = 'Product tidak dapat dihapus karena sudah memiliki riwayat transaksi.';
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                Alert::error('Gagal', $message);
                return back();
            }

            // Hapus serial number terkait jika ada
            $produk->serialNumbers()->delete();

            // Hapus gambar terkait secara permanen jika ada
            if ($produk->img_produk && Storage::disk('public')->exists($produk->img_produk)) {
                Storage::disk('public')->delete($produk->img_produk);
            }

            // Panggil method forceDelete() untuk menghapus permanen dari database
            $produk->forceDelete();

            // Kirim respons JSON jika ini adalah permintaan AJAX
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product berhasil dihapus'
                ]);
            }

            // Respons standar jika bukan AJAX
            Alert::success('Berhasil', 'Product berhasil dihapus');
            return redirect()->route('produk.index');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                // Sertakan pesan error untuk debugging di sisi client jika perlu
                return response()->json(['success' => false, 'message' => 'Gagal menghapus produk: ' . $e->getMessage()], 500);
            }
            Alert::error('Gagal', 'Terjadi kesalahan saat menghapus produk.');
            return back();
        }
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Product::class, 'slug', $request->name_produk);
        return response()->json(['slug' => $slug]);
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('img_produk')) {
            $request->validate([
                'img_produk' => 'required|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            ]);
            $file = $request->file('img_produk');
            // Simpan ke storage/app/public/tmp/produk
            $path = $file->store('tmp/produk', 'public');
            // Kembalikan path sebagai response text, FilePond akan menangkap ini
            return $path;
        }
        // Jika gagal
        return response('Gagal mengunggah.', 500);
    }

    /**
     * Menangani pembatalan unggahan file dari FilePond.
     */
    public function revert(Request $request)
    {
        // FilePond mengirimkan path file sebagai konten body request
        $filePath = $request->getContent();

        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->noContent(); // Berhasil, tidak ada konten untuk dikembalikan
        }

        return response()->json(['error' => 'File not found or path is missing.'], 404);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search');
        // Selalu urutkan berdasarkan name produk untuk konsistensi
        $query = Product::with('pajak')->orderBy('qty', 'asc');

        // Filter berdasarkan kata kunci pencarian
        if ($search) {
            $query->where('name_produk', 'LIKE', '%' . $search . '%');
        }

        // Filter berdasarkan flag 'wajib_seri' jika ada di request
        if ($request->boolean('wajib_seri')) {
            $query->where('wajib_seri', true);
        }

        // Gunakan paginate() untuk mengembalikan hasil yang kompatibel dengan Select2 AJAX (load more)
        return response()->json($query->paginate(10));
    }

    public function cekStock(Request $request)
    {
        $id = $request->query('id');
        $stok = Product::find($id)->qty;
        return response()->json($stok);
    }

    /**
     * Mengambil data produk berdasarkan barcode untuk kasir.
     */
    public function getByBarcode($barcode)
    {
        // Eager load relasi yang dibutuhkan di kasir
        $produk = Product::with(['pajak'])
            ->where('barcode', $barcode)
            ->first();

        if ($produk) {
            // Cek stok
            if ($produk->qty < 1) {
                // Menggunakan status 422 untuk error yang bisa diproses client
                return response()->json(['message' => 'Stock produk habis.'], 422);
            }
            // Mengembalikan data produk jika ditemukan dan stok tersedia
            return response()->json($produk);
        }

        // Mengembalikan error 404 jika produk tidak ditemukan
        return response()->json(['message' => 'Product dengan barcode ini tidak ditemukan.'], 404);
    }


    public function getLowStockNotifications()
    {
        $lowStockProducts = Product::whereColumn('qty', '<=', 'stok_minimum')
            ->orderBy('qty', 'asc')
            ->take(5)
            ->get(['id', 'name_produk', 'slug', 'qty', 'stok_minimum', 'img_produk']);

        $lowStockCount = Product::whereColumn('qty', '<=', 'stok_minimum')->count();

        return response()->json([
            'count' => $lowStockCount,
            'products' => $lowStockProducts->map(function ($produk) {
                return [
                    'name_produk' => \Illuminate\Support\Str::limit($produk->name_produk, 30),
                    'qty' => $produk->qty,
                    'stok_minimum' => $produk->stok_minimum,
                    'img_url' => $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp'),
                    // --- PERUBAHAN DI SINI ---
                    // Sekarang semua link akan mengarah ke halaman laporan stok rendah
                    'url' => route('stok.rendah')
                ];
            })
        ]);
    }

    public function getUnregisteredSerialNotifications()
    {
        // Hitung SN yang BUKAN Terjual atau Hilang
        $subQueryLogic = function ($query) {
            $query->whereNotIn('status', ['Terjual', 'Hilang']);
        };

        $productsNeedingSerials = Product::where('wajib_seri', true)
            ->withCount(['serialNumbers as sn_tercatat_count' => $subQueryLogic])
            // Bandingkan qty dengan total SN yang masih menjadi aset
            ->whereRaw('products.qty > (select count(*) from serial_numbers where products.id = serial_numbers.product_id and status NOT IN (?, ?))', ['Terjual', 'Hilang'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $count = Product::where('wajib_seri', true)
            ->whereRaw('products.qty > (select count(*) from serial_numbers where products.id = serial_numbers.product_id and status NOT IN (?, ?))', ['Terjual', 'Hilang'])
            ->count();

        return response()->json([
            'count' => $count,
            'products' => $productsNeedingSerials->map(function ($produk) {
                $needed = $produk->qty - $produk->sn_tercatat_count;
                return [
                    'name_produk' => \Illuminate\Support\Str::limit($produk->name_produk, 30),
                    'needed' => $needed,
                    'img_url' => $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp'),
                    'url' => route('serialNumber.index', ['produk_slug' => $produk->slug])
                ];
            })
        ]);
    }

    public function allNotifications()
    {
        $lowStockProducts = Product::whereColumn('qty', '<=', 'stok_minimum')
            ->orderBy('qty', 'asc')
            ->get();

        $productsNeedingSerials = Product::where('wajib_seri', true)
            ->withCount(['serialNumbers as sn_tercatat_count' => function ($query) {
                $query->whereNotIn('status', ['Terjual', 'Hilang']);
            }])
            ->whereRaw(
                'products.qty > (select count(*) from serial_numbers where products.id = serial_numbers.product_id and status NOT IN (?, ?))',
                ['Terjual', 'Hilang']
            )
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('content.all', [
            'title' => 'Semua Notifikasi',
            'lowStockProducts' => $lowStockProducts,
            'productsNeedingSerials' => $productsNeedingSerials,
        ]);
    }

    /**
     * Menampilkan halaman laporan produk dengan stok rendah.
     */
    public function laporanStockRendah(Request $request)
    {
        // Subquery untuk mendapatkan tanggal penjualan terakhir
        $lastSaleDateSubquery = SaleItem::select('sales.tanggal_penjualan')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereColumn('sale_items.product_id', 'products.id')
            // PERBAIKAN: Hanya ambil tanggal dari penjualan yang tidak dibatalkan.
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->orderBy('sales.tanggal_penjualan', 'desc')
            ->limit(1);

        $query = Product::with('category')
            ->whereColumn('qty', '<=', 'stok_minimum')
            ->addSelect(['*', 'last_sale_date' => $lastSaleDateSubquery]) // Tambahkan kolom virtual 'last_sale_date'
            ->orderBy('qty', 'asc');

        // Filter berdasarkan pencarian name produk
        if ($request->filled('search')) {
            $query->where('name_produk', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        $products = $query->paginate(15)->withQueryString();
        $kategoris = Category::where('status', 1)->orderBy('name')->get();

        return view('content.inventaris.stok-rendah', [
            'title' => 'Laporan Stock Rendah',
            'products' => $products,
            'kategoris' => $kategoris,
        ]);
    }
}
