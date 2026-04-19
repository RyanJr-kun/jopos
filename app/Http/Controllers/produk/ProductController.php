<?php
// ============================================================
// FILE: app/Http/Controllers/produk/ProductController.php (UPDATED)
// ============================================================
namespace App\Http\Controllers\produk;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Taxe;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantType;
use App\Models\ProductVariantOption;
use App\Models\Warrantie;
use Illuminate\Http\Request;
use App\Models\SaleItem;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class ProductController extends Controller
{
    // -------------------------------------------------------
    // INDEX
    // -------------------------------------------------------
    public function index()
    {
        $request = request();

        $query = Product::with(['category', 'brand', 'unit', 'user', 'primaryImage'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name_product', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            // Filter termasuk sub-kategori dari parent yang dipilih
            $categoryId = $request->input('kategori');
            $childIds = Category::where('parent_id', $categoryId)->pluck('id')->push($categoryId);
            $query->whereIn('category_id', $childIds);
        }

        $products = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('content.produk._produk_table', ['produk' => $products])->render();
        }

        return view('content.produk.index', [
            'produk'    => $products,
            'kategoris' => Category::where('status', 1)
                ->whereNull('parent_id')          // hanya parent untuk filter
                ->whereHas('products')
                ->orderBy('name')->get(),
        ]);
    }

    // -------------------------------------------------------
    // CREATE
    // -------------------------------------------------------
    public function create()
    {
        return view('content.produk.create', [
            // Kirim kategori terstruktur (parent + children) untuk dropdown bertingkat
            'kategoris' => Category::where('status', 1)
                ->whereNull('parent_id')
                ->with(['children' => fn($q) => $q->where('status', 1)->orderBy('name')])
                ->orderBy('name')->get(),
            'brand'   => Brand::where('status', 1)->orderBy('name')->get(),
            'unit'    => Unit::where('status', 1)->orderBy('name')->get(),
            'garansi' => Warrantie::where('status', 1)->orderBy('name')->get(),
            'pajak'   => Taxe::orderBy('name_taxe')->get(),
        ]);
    }

    // -------------------------------------------------------
    // STORE
    // -------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'name_product'  => 'required|string|max:255',
            'slug'          => 'required|string|unique:products,slug',
            'barcode'       => 'nullable|string|unique:products,barcode',
            'sku'           => 'required|string|unique:products,sku',
            'kategori'      => 'required|exists:categories,id',
            'brand'         => 'required|exists:brands,id',
            'unit'          => 'required|exists:units,id',
            'description'   => 'nullable|string',
            'specification' => 'nullable|string',
            'harga_jual'    => 'required|numeric',
            'harga_beli'    => 'required|numeric',
            'qty'           => 'required|integer',
            'garansi'       => 'required|exists:warranties,id',
            'stok_minimum'  => 'required|integer',
            'pajak'         => 'nullable|exists:taxes,id',
            'wajib_seri'    => 'nullable|boolean',

            // Galeri foto (array path tmp dari FilePond)
            'gallery'       => 'nullable|array',
            'gallery.*'     => 'string',
            'primary_image' => 'nullable|string', // path tmp gambar utama

            // Variasi: array of { type_name, options: [value, ...] }
            'variant_types'                  => 'nullable|array',
            'variant_types.*.name'           => 'required_with:variant_types|string|max:100',
            'variant_types.*.options'        => 'required_with:variant_types|array|min:1',
            'variant_types.*.options.*'      => 'string|max:100',

            // Kombinasi variasi
            'variants'                       => 'nullable|array',
            'variants.*.option_ids'          => 'required_with:variants|array',
            'variants.*.sku'                 => 'required_with:variants|string|unique:product_variants,sku',
            'variants.*.barcode'             => 'nullable|string|unique:product_variants,barcode',
            'variants.*.harga_jual'          => 'required_with:variants|numeric',
            'variants.*.harga_beli'          => 'required_with:variants|numeric',
            'variants.*.qty'                 => 'required_with:variants|integer',
            'variants.*.img_variant'         => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {

            // ---- 1. Buat produk utama ----
            $product = Product::create([
                'name_product'  => $request->name_product,
                'slug'          => $request->slug,
                'barcode'       => $request->barcode,
                'sku'           => $request->sku,
                'description'   => $request->description,
                'specification' => $request->specification,
                'harga_jual'    => $request->harga_jual,
                'harga_beli'    => $request->harga_beli,
                'qty'           => $request->qty,
                'stok_minimum'  => $request->stok_minimum,
                'wajib_seri'    => $request->boolean('wajib_seri'),
                'category_id'   => $request->kategori,
                'brand_id'      => $request->brand,
                'unit_id'       => $request->unit,
                'warrantie_id'  => $request->garansi,
                'taxe_id'       => $request->pajak,
                'user_id'       => Auth::id(),
                // img_produk dikosongkan — sekarang pakai tabel product_images
            ]);

            // ---- 2. Simpan galeri foto ----
            $this->saveGallery($product, $request->input('gallery', []), $request->input('primary_image'));

            // ---- 3. Simpan tipe & opsi variasi, lalu kombinasi ----
            if ($request->filled('variant_types')) {
                $this->saveVariants($product, $request->input('variant_types'), $request->input('variants', []));
            }
        });

        return redirect()->route('produk.index')->with('success', 'Product Baru Berhasil Ditambahkan.');
    }

    // -------------------------------------------------------
    // SHOW
    // -------------------------------------------------------
    public function show(Product $produk)
    {
        return view('content.produk.show', [
            'produk' => $produk->load([
                'category',
                'brand',
                'unit',
                'garansi',
                'user',
                'pajak',
                'images',
                'variantTypes.options',
                'variants.options.variantType',
            ])
        ]);
    }

    // -------------------------------------------------------
    // EDIT
    // -------------------------------------------------------
    public function edit(Product $produk)
    {
        return view('content.produk.edit', [
            'produk'   => $produk->load([
                'images',
                'variantTypes.options',
                'variants.options.variantType',
            ]),
            'kategoris' => Category::where('status', 1)
                ->whereNull('parent_id')
                ->with(['children' => fn($q) => $q->where('status', 1)->orderBy('name')])
                ->orderBy('name')->get(),
            'brands'    => Brand::where('status', 1)->orderBy('name')->get(),
            'units'     => Unit::where('status', 1)->orderBy('name')->get(),
            'warranties' => Warrantie::where('status', 1)->orderBy('name')->get(),
            'pajak'     => Taxe::orderBy('name_taxe')->get(),
        ]);
    }

    // -------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------
    public function update(Request $request, Product $produk)
    {
        $request->validate([
            'name_product'  => 'required|string|max:255',
            'slug'          => ['required', 'string', Rule::unique('products', 'slug')->ignore($produk->id)],
            'barcode'       => ['nullable', 'string', Rule::unique('products', 'barcode')->ignore($produk->id)],
            'sku'           => ['required', 'string', Rule::unique('products', 'sku')->ignore($produk->id)],
            'kategori'      => 'required|exists:categories,id',
            'brand'         => 'required|exists:brands,id',
            'unit'          => 'required|exists:units,id',
            'description'   => 'nullable|string',
            'specification' => 'nullable|string',
            'harga_jual'    => 'required|numeric',
            'harga_beli'    => 'required|numeric',
            'qty'           => 'required|integer',
            'garansi'       => 'nullable|exists:warranties,id',
            'stok_minimum'  => 'required|integer',
            'pajak'         => 'nullable|exists:taxes,id',
            'wajib_seri'    => 'nullable|boolean',

            // Galeri
            'gallery'         => 'nullable|array',
            'gallery.*'       => 'string',
            'primary_image'   => 'nullable|string',
            // ID gambar lama yang INGIN DIPERTAHANKAN (yang tidak ada = dihapus)
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'integer',

            // Variasi
            'variant_types'             => 'nullable|array',
            'variant_types.*.name'      => 'required_with:variant_types|string|max:100',
            'variant_types.*.options'   => 'required_with:variant_types|array|min:1',
            'variant_types.*.options.*' => 'string|max:100',

            'variants'              => 'nullable|array',
            'variants.*.id'         => 'nullable|integer|exists:product_variants,id',
            'variants.*.option_ids' => 'required_with:variants|array',
            'variants.*.sku'        => [
                'required_with:variants',
                'string',
                // Izinkan SKU yang sudah ada milik variant ini
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $variantId = $request->input("variants.{$index}.id");
                    $exists = \App\Models\ProductVariant::where('sku', $value)
                        ->when($variantId, fn($q) => $q->where('id', '!=', $variantId))
                        ->exists();
                    if ($exists) $fail("SKU variasi sudah digunakan.");
                }
            ],
            'variants.*.barcode'    => 'nullable|string',
            'variants.*.harga_jual' => 'required_with:variants|numeric',
            'variants.*.harga_beli' => 'required_with:variants|numeric',
            'variants.*.qty'        => 'required_with:variants|integer',
            'variants.*.img_variant' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $produk) {

            // ---- 1. Update produk utama ----
            $produk->update([
                'name_product'  => $request->name_product,
                'slug'          => $request->slug,
                'barcode'       => $request->barcode,
                'sku'           => $request->sku,
                'description'   => $request->description,
                'specification' => $request->specification,
                'harga_jual'    => $request->harga_jual,
                'harga_beli'    => $request->harga_beli,
                'qty'           => $request->qty,
                'stok_minimum'  => $request->stok_minimum,
                'wajib_seri'    => $request->boolean('wajib_seri'),
                'category_id'   => $request->kategori,
                'brand_id'      => $request->brand,
                'unit_id'       => $request->unit,
                'warrantie_id'  => $request->garansi,
                'taxe_id'       => $request->pajak,
                'user_id'       => Auth::id(),
            ]);

            // ---- 2. Update galeri foto ----
            $keepIds = $request->input('existing_images', []);
            // Hapus foto lama yang tidak di-keep
            $produk->images()->whereNotIn('id', $keepIds)->each(function ($img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            });
            // Tambah foto baru
            $this->saveGallery($produk, $request->input('gallery', []), $request->input('primary_image'));

            // ---- 3. Update variasi ----
            if ($request->filled('variant_types')) {
                // Hapus semua tipe lama, buat ulang (simpel & aman untuk edit)
                // Hapus variant lama -> hapus foto variant lama
                $produk->variants()->each(function ($v) {
                    if ($v->img_variant) Storage::disk('public')->delete($v->img_variant);
                    $v->delete();
                });
                $produk->variantTypes()->delete();

                $this->saveVariants($produk, $request->input('variant_types'), $request->input('variants', []));
            } else {
                // Kalau variant_types dikosongkan, berarti hapus semua variasi
                $produk->variants()->each(function ($v) {
                    if ($v->img_variant) Storage::disk('public')->delete($v->img_variant);
                    $v->delete();
                });
                $produk->variantTypes()->delete();
            }
        });

        return redirect()->route('produk.index')->with('success', 'Product Berhasil Diupdate.');
    }

    // -------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------
    public function destroy(Product $produk)
    {
        // Hapus semua foto galeri
        $produk->images()->each(function ($img) {
            Storage::disk('public')->delete($img->path);
        });
        // Hapus foto variasi
        $produk->variants()->each(function ($v) {
            if ($v->img_variant) Storage::disk('public')->delete($v->img_variant);
        });
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Product Berhasil Dihapus.');
    }

    // -------------------------------------------------------
    // HELPER PRIVATE: simpan galeri
    // -------------------------------------------------------
    private function saveGallery(Product $product, array $galleryPaths, ?string $primaryPath): void
    {
        $order = $product->images()->max('sort_order') + 1;

        // Tentukan apakah sudah ada primary
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        // Gabungkan primary_image ke gallery supaya diproses bersamaan
        $allPaths = collect($galleryPaths);
        if ($primaryPath && !$allPaths->contains($primaryPath)) {
            $allPaths->prepend($primaryPath);
        }

        foreach ($allPaths as $tmpPath) {
            if (!$tmpPath || !str_starts_with($tmpPath, 'tmp/')) continue;
            if (!Storage::disk('public')->exists($tmpPath)) continue;

            $newPath = 'produk/gallery/' . basename($tmpPath);
            Storage::disk('public')->move($tmpPath, $newPath);

            $isPrimary = (!$hasPrimary && $tmpPath === $primaryPath)
                || (!$hasPrimary && $order === 1);

            $product->images()->create([
                'path'       => $newPath,
                'is_primary' => $isPrimary,
                'sort_order' => $order++,
            ]);

            if ($isPrimary) $hasPrimary = true;
        }

        // Jika masih belum ada primary, jadikan foto pertama sebagai primary
        if (!$product->images()->where('is_primary', true)->exists()) {
            $first = $product->images()->orderBy('sort_order')->first();
            if ($first) $first->update(['is_primary' => true]);
        }
    }

    // -------------------------------------------------------
    // HELPER PRIVATE: simpan variasi
    // -------------------------------------------------------
    private function saveVariants(Product $product, array $variantTypes, array $variantCombinations): void
    {
        // Buat tipe & simpan mapping name -> [option_value -> option_id]
        $optionMap = []; // ['Warna']['Merah'] = option_id

        foreach ($variantTypes as $typeIndex => $typeData) {
            $type = ProductVariantType::create([
                'product_id' => $product->id,
                'name'       => $typeData['name'],
                'sort_order' => $typeIndex,
            ]);

            foreach ($typeData['options'] as $optIndex => $optValue) {
                $option = ProductVariantOption::create([
                    'variant_type_id' => $type->id,
                    'value'           => $optValue,
                    'sort_order'      => $optIndex,
                ]);
                $optionMap[$typeData['name']][$optValue] = $option->id;
            }
        }

        // Buat kombinasi variant
        foreach ($variantCombinations as $varData) {
            // Tangani foto variant
            $imgPath = null;
            $tmpImg = $varData['img_variant'] ?? null;
            if ($tmpImg && str_starts_with($tmpImg, 'tmp/') && Storage::disk('public')->exists($tmpImg)) {
                $imgPath = 'produk/variants/' . basename($tmpImg);
                Storage::disk('public')->move($tmpImg, $imgPath);
            }

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'sku'        => $varData['sku'],
                'barcode'    => $varData['barcode'] ?? null,
                'harga_jual' => $varData['harga_jual'],
                'harga_beli' => $varData['harga_beli'],
                'qty'        => $varData['qty'],
                'img_variant' => $imgPath,
                'is_active'  => true,
            ]);

            // Attach option IDs ke pivot
            // option_ids berisi array of option_id (dikirim dari frontend)
            if (!empty($varData['option_ids'])) {
                $variant->options()->attach($varData['option_ids']);
            }
        }
    }

    // -------------------------------------------------------
    // UPLOAD (FilePond) — sama seperti sebelumnya
    // -------------------------------------------------------
    public function upload(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:jpg,jpeg,png,webp,svg|max:2048']);

        $path = $request->file('file')->store('tmp', 'public');
        if ($path) {
            return $path;
        }
        return response('Gagal mengunggah.', 500);
    }

    public function revert(Request $request)
    {
        $filePath = $request->getContent();
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
            return response()->noContent();
        }
        return response()->json(['error' => 'File not found or path is missing.'], 404);
    }

    // -------------------------------------------------------
    // CHECK SLUG
    // -------------------------------------------------------
    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Product::class, 'slug', $request->name_product);
        return response()->json(['slug' => $slug]);
    }

    // -------------------------------------------------------
    // GET DATA (untuk Select2 / AJAX)
    // -------------------------------------------------------
    public function getData(Request $request)
    {
        $search = $request->query('search');
        $query  = Product::with('pajak')->orderBy('qty', 'asc');

        if ($search) {
            $query->where('name_product', 'LIKE', '%' . $search . '%');
        }
        if ($request->boolean('wajib_seri')) {
            $query->where('wajib_seri', true);
        }

        return response()->json($query->paginate(10));
    }

    public function cekStock(Request $request)
    {
        $stok = Product::findOrFail($request->query('id'))->qty;
        return response()->json($stok);
    }

    public function getByBarcode($barcode)
    {
        $produk = Product::with(['pajak'])->where('barcode', $barcode)->first();

        if ($produk) {
            if ($produk->qty < 1) {
                return response()->json(['message' => 'Stock produk habis.'], 422);
            }
            return response()->json($produk);
        }
        return response()->json(['message' => 'Product dengan barcode ini tidak ditemukan.'], 404);
    }

    // -------------------------------------------------------
    // NOTIFIKASI & LAPORAN (tidak diubah)
    // -------------------------------------------------------
    public function getLowStockNotifications()
    {
        $lowStockProducts = Product::whereColumn('qty', '<=', 'stok_minimum')
            ->orderBy('qty', 'asc')->take(5)
            ->get(['id', 'name_product', 'slug', 'qty', 'stok_minimum', 'img_produk']);

        $lowStockCount = Product::whereColumn('qty', '<=', 'stok_minimum')->count();

        return response()->json([
            'count'    => $lowStockCount,
            'products' => $lowStockProducts->map(function ($produk) {
                return [
                    'name_product' => \Illuminate\Support\Str::limit($produk->name_product, 30),
                    'qty'          => $produk->qty,
                    'stok_minimum' => $produk->stok_minimum,
                    'img_url'      => $produk->img_produk
                        ? asset('storage/' . $produk->img_produk)
                        : asset('assets/img/produk.webp'),
                    'url' => route('stok.rendah')
                ];
            })
        ]);
    }

    public function getUnregisteredSerialNotifications()
    {
        $subQueryLogic = fn($query) => $query->whereNotIn('status', ['Terjual', 'Hilang']);

        $productsNeedingSerials = Product::where('wajib_seri', true)
            ->withCount(['serialNumbers as sn_tercatat_count' => $subQueryLogic])
            ->whereRaw('products.qty > (select count(*) from serial_numbers where products.id = serial_numbers.product_id and status NOT IN (?, ?))', ['Terjual', 'Hilang'])
            ->orderBy('updated_at', 'desc')->take(5)->get();

        $count = Product::where('wajib_seri', true)
            ->whereRaw('products.qty > (select count(*) from serial_numbers where products.id = serial_numbers.product_id and status NOT IN (?, ?))', ['Terjual', 'Hilang'])
            ->count();

        return response()->json([
            'count'    => $count,
            'products' => $productsNeedingSerials->map(function ($produk) {
                return [
                    'name_product' => \Illuminate\Support\Str::limit($produk->name_product, 30),
                    'needed'       => $produk->qty - $produk->sn_tercatat_count,
                    'img_url'      => $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.webp'),
                    'url'          => route('serialNumber.index', ['produk_slug' => $produk->slug])
                ];
            })
        ]);
    }

    public function allNotifications()
    {
        $lowStockProducts = Product::whereColumn('qty', '<=', 'stok_minimum')->orderBy('qty', 'asc')->get();
        $productsNeedingSerials = Product::where('wajib_seri', true)
            ->withCount(['serialNumbers as sn_tercatat_count' => fn($q) => $q->whereNotIn('status', ['Terjual', 'Hilang'])])
            ->whereRaw('products.qty > (select count(*) from serial_numbers where products.id = serial_numbers.product_id and status NOT IN (?, ?))', ['Terjual', 'Hilang'])
            ->orderBy('updated_at', 'desc')->get();

        return view('content.all', [
            'title'                  => 'Semua Notifikasi',
            'lowStockProducts'       => $lowStockProducts,
            'productsNeedingSerials' => $productsNeedingSerials,
        ]);
    }

    public function laporanStockRendah(Request $request)
    {
        $lastSaleDateSubquery = SaleItem::select('sales.tanggal_penjualan')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereColumn('sale_items.product_id', 'products.id')
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->orderBy('sales.tanggal_penjualan', 'desc')
            ->limit(1);

        $query = Product::with('category')
            ->whereColumn('qty', '<=', 'stok_minimum')
            ->addSelect(['*', 'last_sale_date' => $lastSaleDateSubquery])
            ->orderBy('qty', 'asc');

        if ($request->filled('search')) {
            $query->where('name_product', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('kategori')) {
            $query->where('category_id', $request->kategori);
        }

        $products  = $query->paginate(15)->withQueryString();
        $kategoris = Category::where('status', 1)->orderBy('name')->get();

        return view('content.inventaris.stok-rendah', [
            'title'     => 'Laporan Stock Rendah',
            'products'  => $products,
            'kategoris' => $kategoris,
        ]);
    }
}
