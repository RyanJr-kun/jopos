<?php

namespace App\Http\Controllers\produk;

use App\Http\Controllers\Controller;

use App\Models\Unit;
use App\Models\Brand;
use App\Models\Pajak;
use App\Models\Produk;
use App\Models\Garansi;
use Illuminate\Http\Request;
use App\Models\ItemPenjualan;
use App\Models\KategoriProduk;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil request
        $request = request();

        // Mulai query builder
        $query = Produk::with(['kategori_produk', 'brand', 'unit', 'user'])->latest();

        // Terapkan filter pencarian jika ada input 'search'
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Terapkan filter kategori jika ada input 'kategori'
        if ($request->filled('kategori')) {
            $query->where('kategori_produk_id', $request->input('kategori'));
        }

        $produks = $query->paginate(10)->withQueryString();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('dashboard.produk.produk._produk_table', ['produk' => $produks])->render();
        }

        return view('dashboard.produk.produk.index', [
            'produk' => $produks,
            'kategoris' => KategoriProduk::where('status', 1)->whereHas('produks')->orderBy('nama')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.produk.produk.create', [
            'kategori' => KategoriProduk::where('status', 1)->orderBy('nama')->get(),
            'brand' => Brand::where('status', 1)->orderBy('nama')->get(),
            'unit' => Unit::where('status', 1)->orderBy('nama')->get(),
            'garansi' => Garansi::where('status', 1)->orderBy('nama')->get(),
            'pajak' => Pajak::orderBy('nama_pajak')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_produk' => 'required|string|max:255',
            'slug' => 'required|string|unique:produks,slug',
            'barcode' => 'nullable|string|unique:produks,barcode',
            'sku' => 'required|string|unique:produks,sku',
            'kategori' => 'required|exists:kategori_produks,id',
            'brand' => 'required|exists:brands,id',
            'unit' => 'required|exists:units,id',
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'qty' => 'required|integer',
            'garansi' => 'required|exists:garansis,id',
            'stok_minimum' => 'required|integer',
            'pajak' => 'required|exists:pajaks,id',
            'img_produk' => 'nullable|string',
            'wajib_seri' => 'nullable|boolean',
        ]);

        $validatedData['user_id'] = Auth::id();
        $validatedData['kategori_produk_id'] = $validatedData['kategori'];
        $validatedData['brand_id'] = $validatedData['brand'];
        $validatedData['unit_id'] = $validatedData['unit'];
        $validatedData['pajak_id'] = $validatedData['pajak'];
        $validatedData['garansi_id'] = $validatedData['garansi'];
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

        Produk::create($validatedData);
        Alert::success('Berhasil', 'Produk Baru Berhasil Ditambahkan.');
        return redirect()->route('produk.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Produk $produk)
    {
        return view('dashboard.produk.produk.show', [
            'produk' => $produk->load(['kategori_produk', 'brand', 'unit', 'garansi', 'user', 'pajak'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produk $produk)
    {
        return view('dashboard.produk.produk.edit', [
            'produk' => $produk,
            'kategoris' => KategoriProduk::where('status', 1)->orderBy('nama')->get(),
            'brands' => Brand::where('status', 1)->orderBy('nama')->get(),
            'units' => Unit::where('status', 1)->orderBy('nama')->get(),
            'garansis' => Garansi::where('status', 1)->orderBy('nama')->get(),
            'pajak' => Pajak::orderBy('nama_pajak')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produk $produk)
    {
        $rules = [
            'nama_produk' => 'required|string|max:255',
            'kategori' => 'required|exists:kategori_produks,id',
            'brand' => 'required|exists:brands,id',
            'unit' => 'required|exists:units,id',
            'pajak' => 'required|exists:pajaks,id',
            'deskripsi' => 'nullable|string',
            'harga_jual' => 'required|numeric',
            'harga_beli' => 'required|numeric',
            'qty' => 'required|integer',
            'garansi' => 'nullable|exists:garansis,id',
            'stok_minimum' => 'required|integer',
            'img_produk' => 'nullable|string',
            'slug' => ['required', 'string', Rule::unique('produks', 'slug')->ignore($produk->id)],
            'barcode' => ['nullable', 'string', Rule::unique('produks', 'barcode')->ignore($produk->id)],
            'sku' => ['required', 'string', Rule::unique('produks', 'sku')->ignore($produk->id)],
            'wajib_seri' => 'nullable|boolean',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['user_id'] = Auth::id();
        $validatedData['kategori_produk_id'] = $validatedData['kategori'];
        $validatedData['brand_id'] = $validatedData['brand'];
        $validatedData['unit_id'] = $validatedData['unit'];
        $validatedData['garansi_id'] = $validatedData['garansi'];
        $validatedData['pajak_id'] = $validatedData['pajak'];
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
        Alert::success('Berhasil', 'Data Produk Berhasil Diperbarui.');
        return redirect()->route('produk.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Produk $produk)
    {
        try {
            // Pengecekan relasi sebelum menghapus
            if ($produk->itemPenjualans()->exists() || $produk->pembelianDetails()->exists()) {
                $message = 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi.';
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
                    'message' => 'Produk berhasil dihapus'
                ]);
            }

            // Respons standar jika bukan AJAX
            Alert::success('Berhasil', 'Produk berhasil dihapus');
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
        $slug = SlugService::createSlug(Produk::class, 'slug', $request->nama_produk);
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
        // Selalu urutkan berdasarkan nama produk untuk konsistensi
        $query = Produk::with('pajak')->orderBy('qty', 'asc');

        // Filter berdasarkan kata kunci pencarian
        if ($search) {
            $query->where('nama_produk', 'LIKE', '%' . $search . '%');
        }

        // Filter berdasarkan flag 'wajib_seri' jika ada di request
        if ($request->boolean('wajib_seri')) {
            $query->where('wajib_seri', true);
        }

        // Gunakan paginate() untuk mengembalikan hasil yang kompatibel dengan Select2 AJAX (load more)
        return response()->json($query->paginate(10));
    }

    public function cekStok(Request $request)
    {
        $id = $request->query('id');
        $stok = Produk::find($id)->qty;
        return response()->json($stok);
    }

    /**
     * Mengambil data produk berdasarkan barcode untuk kasir.
     */
    public function getByBarcode($barcode)
    {
        // Eager load relasi yang dibutuhkan di kasir
        $produk = Produk::with(['pajak'])
            ->where('barcode', $barcode)
            ->first();

        if ($produk) {
            // Cek stok
            if ($produk->qty < 1) {
                // Menggunakan status 422 untuk error yang bisa diproses client
                return response()->json(['message' => 'Stok produk habis.'], 422);
            }
            // Mengembalikan data produk jika ditemukan dan stok tersedia
            return response()->json($produk);
        }

        // Mengembalikan error 404 jika produk tidak ditemukan
        return response()->json(['message' => 'Produk dengan barcode ini tidak ditemukan.'], 404);
    }


    public function getLowStockNotifications()
    {
        $lowStockProducts = Produk::whereColumn('qty', '<=', 'stok_minimum')
            ->orderBy('qty', 'asc')
            ->take(5)
            ->get(['id', 'nama_produk', 'slug', 'qty', 'stok_minimum', 'img_produk']);

        $lowStockCount = Produk::whereColumn('qty', '<=', 'stok_minimum')->count();

        return response()->json([
            'count' => $lowStockCount,
            'products' => $lowStockProducts->map(function ($produk) {
                return [
                    'nama_produk' => \Illuminate\Support\Str::limit($produk->nama_produk, 30),
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

        $productsNeedingSerials = Produk::where('wajib_seri', true)
            ->withCount(['serialNumbers as sn_tercatat_count' => $subQueryLogic])
            // Bandingkan qty dengan total SN yang masih menjadi aset
            ->whereRaw('produks.qty > (select count(*) from serial_numbers where produks.id = serial_numbers.produk_id and status NOT IN (?, ?))', ['Terjual', 'Hilang'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $count = Produk::where('wajib_seri', true)
            ->whereRaw('produks.qty > (select count(*) from serial_numbers where produks.id = serial_numbers.produk_id and status NOT IN (?, ?))', ['Terjual', 'Hilang'])
            ->count();

        return response()->json([
            'count' => $count,
            'products' => $productsNeedingSerials->map(function ($produk) {
                $needed = $produk->qty - $produk->sn_tercatat_count;
                return [
                    'nama_produk' => \Illuminate\Support\Str::limit($produk->nama_produk, 30),
                    'needed' => $needed,
                    'img_url' => $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp'),
                    'url' => route('serialNumber.index', ['produk_slug' => $produk->slug])
                ];
            })
        ]);
    }

    public function allNotifications()
    {
        $lowStockProducts = Produk::whereColumn('qty', '<=', 'stok_minimum')
            ->orderBy('qty', 'asc')
            ->get();

        $productsNeedingSerials = Produk::where('wajib_seri', true)
            ->withCount(['serialNumbers as sn_tercatat_count' => function ($query) {
                $query->whereNotIn('status', ['Terjual', 'Hilang']);
            }])
            ->whereRaw(
                'produks.qty > (select count(*) from serial_numbers where produks.id = serial_numbers.produk_id and status NOT IN (?, ?))',
                ['Terjual', 'Hilang']
            )
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('dashboard.all', [
            'title' => 'Semua Notifikasi',
            'lowStockProducts' => $lowStockProducts,
            'productsNeedingSerials' => $productsNeedingSerials,
        ]);
    }

    /**
     * Menampilkan halaman laporan produk dengan stok rendah.
     */
    public function laporanStokRendah(Request $request)
    {
        // Subquery untuk mendapatkan tanggal penjualan terakhir
        $lastSaleDateSubquery = ItemPenjualan::select('penjualans.tanggal_penjualan')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->whereColumn('item_penjualans.produk_id', 'produks.id')
            // PERBAIKAN: Hanya ambil tanggal dari penjualan yang tidak dibatalkan.
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            ->orderBy('penjualans.tanggal_penjualan', 'desc')
            ->limit(1);

        $query = Produk::with('kategori_produk')
            ->whereColumn('qty', '<=', 'stok_minimum')
            ->addSelect(['*', 'last_sale_date' => $lastSaleDateSubquery]) // Tambahkan kolom virtual 'last_sale_date'
            ->orderBy('qty', 'asc');

        // Filter berdasarkan pencarian nama produk
        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('kategori_produk_id', $request->kategori);
        }

        $produks = $query->paginate(15)->withQueryString();
        $kategoris = KategoriProduk::where('status', 1)->orderBy('nama')->get();

        return view('dashboard.inventaris.stok-rendah', [
            'title' => 'Laporan Stok Rendah',
            'produks' => $produks,
            'kategoris' => $kategoris,
        ]);
    }
}
