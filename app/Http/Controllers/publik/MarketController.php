<?php

namespace App\Http\Controllers\publik;

use App\Http\Controllers\Controller;

use App\Enums\BannerPosition;
use App\Models\Banner;
use App\Models\Promo;
use App\Models\Produk;
use App\Models\KategoriProduk;
use App\Models\ProfilToko;
use Illuminate\Support\Facades\DB;

class MarketController extends Controller
{
    /**
     * Menampilkan halaman utama (homepage) web market.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil produk terbaru dengan eager loading untuk performa
        $produks = Produk::with(['kategori_produk', 'unit', 'brand', 'promos'])
            ->latest()
            ->paginate(10);

        // Ambil banner yang aktif untuk Main Carousel, urutkan berdasarkan urutan
        $mainBanners = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::MAIN_CAROUSEL)
            ->orderBy('urutan')
            ->get();

        // Ambil banner yang aktif untuk Promo Vertikal, urutkan berdasarkan urutan
        $promoVertikalBanners = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::PROMO_VERTIKAL)
            ->orderBy('urutan')
            ->get();

        // Ambil banner yang aktif untuk Bestseller, urutkan berdasarkan urutan
        $bestsellerBanners = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::BESTSELLER)
            ->orderBy('urutan')
            ->get();

        // Ambil promo yang aktif dan sedang berjalan saat ini
        $promos = Promo::where('status', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->latest()
            ->get();

        // Ambil kategori produk yang memiliki produk untuk ditampilkan di menu header.
        $kategorisForMenu = KategoriProduk::withCount('produks')
            ->whereHas('produks')
            ->orderBy('nama')
            ->get();

        // Ambil 6 produk terlaris sepanjang waktu
        $produkTerlaris = Produk::with(['unit'])
            ->select('produks.*', DB::raw('SUM(item_penjualans.jumlah) as total_terjual'))
            ->join('item_penjualans', 'produks.id', '=', 'item_penjualans.produk_id')
            ->join('penjualans', 'item_penjualans.penjualan_id', '=', 'penjualans.id')
            ->where('penjualans.status_pembayaran', '!=', 'Dibatalkan')
            // PERBAIKAN: Tambahkan semua kolom yang dipilih ke GROUP BY untuk kompatibilitas dengan mode ONLY_FULL_GROUP_BY
            ->groupBy(
                'produks.id',
                'produks.nama_produk',
                'produks.slug',
                'produks.barcode',
                'produks.sku',
                'produks.kategori_produk_id',
                'produks.brand_id',
                'produks.unit_id',
                'produks.deskripsi',
                'produks.harga_jual',
                'produks.harga_beli',
                'produks.qty',
                'produks.garansi_id',
                'produks.stok_minimum',
                'produks.pajak_id',
                'produks.img_produk',
                'produks.wajib_seri',
                'produks.user_id',
                'produks.created_at',
                'produks.updated_at'
            )
            ->orderByDesc('total_terjual')
            ->limit(6)
            ->get();

        $produkPromo = Produk::with(['unit', 'promos'])
            ->whereHas('promos', function ($query) {
                $query->where('status', true)
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_berakhir', '>=', now());
            })
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('market.beranda', [
            'title' => 'Beranda',
            'produks' => $produks,
            'mainBanners' => $mainBanners,
            'promoVertikalBanners' => $promoVertikalBanners,
            'bestsellerBanners' => $bestsellerBanners,
            'produkTerlaris' => $produkTerlaris,
            'promos' => $promos,
            'kategoris' => $kategorisForMenu, // Kirim data kategori ke view
            'produkPromo' => $produkPromo
        ]);
    }

    /**
     * Menampilkan halaman daftar semua produk dengan filter dan paginasi.
     *
     * @return \Illuminate\View\View
     */

    public function produk(\Illuminate\Http\Request $request)
    {
        $query = Produk::with(['kategori_produk', 'unit', 'brand', 'promos']);

        // Filter berdasarkan Kategori (dari slug)
        if ($request->filled('kategori')) {
            $query->whereHas('kategori_produk', function ($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }

        // Filter berdasarkan pencarian keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_produk', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Sorting
        switch ($request->get('sort')) {
            case 'harga_asc':
                $query->orderBy('harga_jual', 'asc');
                break;
            case 'harga_desc':
                $query->orderBy('harga_jual', 'desc');
                break;
            default:
                $query->latest(); // Default: terbaru
        }

        $produks = $query->paginate(12)->withQueryString();
        $kategorisForFilter = KategoriProduk::whereHas('produks')->orderBy('nama')->get();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('market._produk_list', compact('produks'))->render();
        }

        return view('market.produk', compact('produks', 'kategorisForFilter'));
    }

    /**
     * Menampilkan halaman detail satu produk berdasarkan slug atau ID.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function produkDetail($slug)
    {
        // PERBAIKAN: Eager load semua relasi yang mungkin ditampilkan di halaman detail.
        $produk = Produk::with(['kategori_produk', 'brand', 'unit', 'garansi', 'pajak', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();
        $produkSerupa = Produk::with('unit', 'promos')
            ->where('kategori_produk_id', $produk->kategori_produk_id)
            ->where('id', '!=', $produk->id)
            ->inRandomOrder()
            ->limit(5)
            ->get();


        return view('market.produkdetail', compact('produk', 'produkSerupa'));
    }
    public function layanan()
    {
        return view('market.layanan', [
            'title' => 'Tentang Kami',
            'title' => 'Layanan Kami',
        ]);
    }

    public function tentang()
    {
        $profil = ProfilToko::first();

        return view('market.tentang', [
            'title' => 'Tentang Kami',
            'profils' => $profil
        ]);
    }

    /**
     * Menangani permintaan live search dari header.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function liveSearch(\Illuminate\Http\Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return response()->json(['produks' => [], 'total' => 0]);
        }

        $produks = Produk::with(['kategori_produk', 'brand'])
            ->where('nama_produk', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->limit(5) // Batasi hasil untuk live search
            ->get();

        return response()->json(['produks' => $produks, 'total' => $produks->count()]);
    }
}
