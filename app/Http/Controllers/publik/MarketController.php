<?php

namespace App\Http\Controllers\publik;

use App\Http\Controllers\Controller;

use App\Enums\BannerPosition;
use App\Models\Banner;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\Category;
use App\Models\StoreSetting;
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
        $products = Product::with(['category', 'unit', 'brand', 'promotions'])
            ->latest()
            ->paginate(10);

        // Ambil banner yang aktif untuk Main Carousel, urutkan berdasarkan urutan
        $mainBanners = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::MAIN_CAROUSEL)
            ->orderBy('urutan')
            ->get();

        // Ambil banner yang aktif untuk Promotion Vertikal, urutkan berdasarkan urutan
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
        $promotions = Promotion::where('status', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->latest()
            ->get();

        // Ambil kategori produk yang memiliki produk untuk ditampilkan di menu header.
        $kategorisForMenu = Category::withCount('products')
            ->whereHas('products')
            ->orderBy('name')
            ->get();

        // Ambil 6 produk terlaris sepanjang waktu
        $produkTerlaris = Product::with(['unit'])
            ->select('products.*', DB::raw('SUM(sale_items.jumlah) as total_terjual'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            // PERBAIKAN: Tambahkan semua kolom yang dipilih ke GROUP BY untuk kompatibilitas dengan mode ONLY_FULL_GROUP_BY
            ->groupBy(
                'products.id',
                'products.name_produk',
                'products.slug',
                'products.barcode',
                'products.sku',
                'products.category_id',
                'products.brand_id',
                'products.unit_id',
                'products.description',
                'products.harga_jual',
                'products.harga_beli',
                'products.qty',
                'products.warrantie_id',
                'products.stok_minimum',
                'products.taxe_id',
                'products.img_produk',
                'products.wajib_seri',
                'products.user_id',
                'products.created_at',
                'products.updated_at'
            )
            ->orderByDesc('total_terjual')
            ->limit(6)
            ->get();

        $produkPromotion = Product::with(['unit', 'promotions'])
            ->whereHas('promotions', function ($query) {
                $query->where('status', true)
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_berakhir', '>=', now());
            })
            ->inRandomOrder()
            ->limit(8)
            ->get();

        return view('content.market.beranda', [
            'title' => 'Beranda',
            'products' => $products,
            'mainBanners' => $mainBanners,
            'promoVertikalBanners' => $promoVertikalBanners,
            'bestsellerBanners' => $bestsellerBanners,
            'produkTerlaris' => $produkTerlaris,
            'promotions' => $promotions,
            'kategoris' => $kategorisForMenu, // Kirim data kategori ke view
            'produkPromotion' => $produkPromotion
        ]);
    }

    /**
     * Menampilkan halaman daftar semua produk dengan filter dan paginasi.
     *
     * @return \Illuminate\View\View
     */

    public function produk(\Illuminate\Http\Request $request)
    {
        $query = Product::with(['category', 'unit', 'brand', 'promotions']);

        // Filter berdasarkan Kategori (dari slug)
        if ($request->filled('kategori')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }

        // Filter berdasarkan pencarian keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_produk', 'like', "%{$search}%")
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

        $products = $query->paginate(12)->withQueryString();
        $kategorisForFilter = Category::whereHas('products')->orderBy('name')->get();

        // Jika ini adalah request AJAX, kembalikan hanya bagian tabelnya
        if ($request->ajax()) {
            return view('content.market._produk_list', compact('products'))->render();
        }

        return view('content.market.produk', compact('products', 'kategorisForFilter'));
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
        $produk = Product::with(['category', 'brand', 'unit', 'garansi', 'pajak', 'user'])
            ->where('slug', $slug)
            ->firstOrFail();
        $produkSerupa = Product::with('unit', 'promotions')
            ->where('category_id', $produk->category_id)
            ->where('id', '!=', $produk->id)
            ->inRandomOrder()
            ->limit(5)
            ->get();


        return view('content.market.produkdetail', compact('produk', 'produkSerupa'));
    }
    public function layanan()
    {
        return view('content.market.layanan', [
            'title' => 'Tentang Kami',
            'title' => 'Layanan Kami',
        ]);
    }

    public function tentang()
    {
        $profil = StoreSetting::first();

        return view('content.market.tentang', [
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
            return response()->json(['products' => [], 'total' => 0]);
        }

        $products = Product::with(['category', 'brand'])
            ->where('name_produk', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->limit(5) // Batasi hasil untuk live search
            ->get();

        return response()->json(['products' => $products, 'total' => $products->count()]);
    }
}
