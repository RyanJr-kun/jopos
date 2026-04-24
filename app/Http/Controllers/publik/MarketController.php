<?php

namespace App\Http\Controllers\publik;

use App\Enums\BannerPosition;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Stores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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
        $products = Product::with(['category', 'unit', 'brand', 'promotions', 'primaryImage'])
            ->latest()
            ->paginate(10);

        // Ambil banner yang aktif untuk Main Carousel, urutkan berdasarkan urutan
        $mainImg = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::MAIN)
            ->orderBy('urutan')
            ->get();
        $main2Img = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::MAIN2)
            ->orderBy('urutan')
            ->get();
        $main3Img = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::MAIN3)
            ->orderBy('urutan')
            ->get();

        // Ambil banner yang aktif untuk Promotion Vertikal, urutkan berdasarkan urutan
        $promoImg = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::PROMO)
            ->orderBy('urutan')
            ->get();

        // Ambil banner yang aktif untuk Bestseller, urutkan berdasarkan urutan
        $bestsellerImg = Banner::where('is_active', true)
            ->where('posisi', BannerPosition::BESTSELLER)
            ->orderBy('urutan')
            ->get();

        // Ambil promo yang aktif dan sedang berjalan saat ini
        $promotions = Promotion::where('status', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->latest()
            ->get();

        // Ambil 6 produk terlaris sepanjang waktu
        $produkTerlaris = Product::with(['unit', 'promotions'])
            ->select('products.*', DB::raw('SUM(sale_items.jumlah) as total_terjual'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            // PERBAIKAN: Tambahkan semua kolom yang dipilih ke GROUP BY untuk kompatibilitas dengan mode ONLY_FULL_GROUP_BY
            ->groupBy(
                'products.id',
                'products.name_product',
                'products.slug',
                'products.barcode',
                'products.sku',
                'products.category_id',
                'products.brand_id',
                'products.unit_id',
                'products.specification',
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

        $kategoris = Category::with('children')
            ->whereNull('parent_id')
            ->get();

        $googleData = Cache::remember('google_reviews_jocomputer', 1440, function () {
        $placeId = env('GOOGLE_MAPS_PLACE_ID'); // Taruh di .env
        $apiKey = env('GOOGLE_MAPS_API_KEY');   // Taruh di .env

        $response = Http::get("https://maps.googleapis.com/maps/api/place/details/json", [
            'place_id' => $placeId,
            'fields'   => 'rating,user_ratings_total,reviews',
            'key'      => $apiKey,
            'language' => 'id' // Meminta ulasan dalam bahasa Indonesia
        ]);

        if ($response->successful() && isset($response['result'])) {
            return $response['result'];
        }

        return null; // Fallback jika API gagal
    });

    // Format ulang data agar sesuai dengan struktur Blade Anda
    $reviewSources = [];
    $googleRating = 0;
    $googleTotal = 0;

    if ($googleData) {
        $googleRating = $googleData['rating'] ?? 0;
        $googleTotal  = $googleData['user_ratings_total'] ?? 0;

        if (isset($googleData['reviews'])) {
            foreach ($googleData['reviews'] as $review) {
                // Buat inisial dari nama
                $initials = (string) Str::of($review['author_name'])->explode(' ')->map(fn($n) => substr($n, 0, 1))->take(2)->join('');

                $reviewSources[] = [
                    'source'   => 'google',
                    'name'     => $review['author_name'],
                    'avatar'   => $initials,
                    'rating'   => $review['rating'],
                    'date'     => $review['relative_time_description'], // Contoh: "2 minggu lalu"
                    'text'     => $review['text'],
                    'verified' => false,
                ];
            }
        }
    }

        return view('content.market.beranda', [
            'title' => 'Beranda',
            'products' => $products,
            'mainImg' => $mainImg,
            'main2Img' => $main2Img,
            'main3Img' => $main3Img,
            'promoImg' => $promoImg,
            'bestsellerImg' => $bestsellerImg,
            'produkTerlaris' => $produkTerlaris,
            'promotions' => $promotions,
            'produkPromotion' => $produkPromotion,
            'kategoris' => $kategoris
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

        // Filter Kategori 
        if ($request->filled('kategori')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }

        // Filter Brand (checkbox — bisa multiple)
        if ($request->filled('brand')) {
            $brandIds = (array) $request->brand;
            $query->whereIn('brand_id', $brandIds);
        }

        // Filter Pencarian Multi-fungsi (Nama, SKU, Brand, atau Kategori)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_product', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    // Mencari berdasarkan nama Brand
                    ->orWhereHas('brand', function ($b) use ($search) {
                        $b->where('name', 'like', "%{$search}%");
                    })
                    // Mencari berdasarkan nama Kategori
                    ->orWhereHas('category', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        switch ($request->get('sort')) {
            case 'nama_asc':
                $query->orderBy('name_product', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('name_product', 'desc');
                break;
            case 'terpopuler':
                $query->addSelect([
                    'total_terjual' => DB::table('sale_items')
                        ->leftJoin('sales', 'sale_items.sale_id', '=', 'sales.id')
                        ->whereColumn('sale_items.product_id', 'products.id')
                        ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
                        ->selectRaw('COALESCE(SUM(sale_items.jumlah), 0)')
                ])->orderByDesc('total_terjual');
                break;
            case 'harga_asc':
                $query->orderBy('harga_jual', 'asc');
                break;
            case 'harga_desc':
                $query->orderBy('harga_jual', 'desc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(20)->withQueryString();
        $kategorisForFilter = Category::whereHas('products')->orderBy('name')->get();

        // AJAX: kembalikan hanya partial
        if ($request->ajax()) {
            return view('content.market._produk_list', compact('products'))->render();
        }

        $kategoris = Category::with('children')->whereNull('parent_id')->get();

        // ── Ambil semua brand yang memiliki produk, urutkan nama ──────
        // Gunakan model Brand jika ada, atau ambil via join dari products
        $brands = Brand::whereHas('products')
            ->orderBy('name')
            ->get();
        // Catatan: jika nama model Brand berbeda (misal "Brands"), sesuaikan.
        // Fallback alternatif jika tidak ada model Brand:
        // $brands = \App\Models\Brand::orderBy('name')->get();

        return view('content.market.produk', compact(
            'products',
            'kategorisForFilter',
            'kategoris',
            'brands'          // ← Data brand dikirim ke view
        ));
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
        $produk = Product::with(['category', 'brand', 'unit', 'garansi', 'pajak', 'user', 'images'])
            ->where('slug', $slug)
            ->firstOrFail();
        $produkSerupa = Product::with('unit', 'promotions')
            ->where('category_id', $produk->category_id)
            ->where('id', '!=', $produk->id)
            ->inRandomOrder()
            ->limit(5)
            ->get();

        $kategoris = Category::with('children')->whereNull('parent_id')->get();

        return view('content.market.produkdetail', compact('produk', 'produkSerupa', 'kategoris'));
    }

    public function layanan()
    {
        $kategoris = Category::with('children')->whereNull('parent_id')->get();

        return view('content.market.layanan', [
            'title'    => 'Layanan Kami',
            'kategoris' => $kategoris,
        ]);
    }

    public function tentang(Request $request)
    {
        // Mulai query
        $query = Stores::query();

        // 1. Filter Pencarian berdasarkan Nama Toko
        if ($request->filled('search')) {
            $query->where('name_toko', 'like', '%' . $request->search . '%');
        }

        // 2. Filter Select berdasarkan Daerah
        if ($request->filled('daerah')) {
            $query->where('daerah', $request->daerah);
        }

        // Eksekusi query
        $profils = $query->get();

        // 3. Jika Request berasal dari AJAX
        if ($request->ajax()) {
            // Kembalikan data dalam bentuk JSON berisi potongan HTML dan jumlah data
            return response()->json([
                // Render file blade partial dan ubah jadi string HTML
                'html'  => view('content.market._list_toko', compact('profils'))->render(),
                'count' => $profils->count()
            ]);
        }

        $kategoris = Category::with('children')->whereNull('parent_id')->get();

        // 4. Jika Request biasa (Load halaman pertama kali)
        return view('content.market.tentang', compact('profils', 'kategoris'));
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

        $products = Product::with(['category', 'brand', 'promotions'])
            ->where(function ($q) use ($query) {
                $q->where('name_product', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($product) {
                // Find an active promotion for this product, if any
                $activePromo = $product->promotions
                    ->where('status', true)
                    ->where('tanggal_mulai', '<=', now())
                    ->where('tanggal_berakhir', '>=', now())
                    ->first();

                // Compute harga_diskon based on promotion type
                if ($activePromo) {
                    $product->harga_diskon = $activePromo->type === 'percentage'
                        ? $product->harga_jual - ($product->harga_jual * $activePromo->nilai_diskon / 100)
                        : $product->harga_jual - $activePromo->nilai_diskon;
                } else {
                    $product->harga_diskon = null;
                }

                return $product;
            });

        return response()->json(['products' => $products, 'total' => $products->count()]);
    }
}
