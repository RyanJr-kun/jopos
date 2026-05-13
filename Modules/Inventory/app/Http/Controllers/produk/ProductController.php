<?php

namespace Modules\Inventory\Http\Controllers\produk;

use \Cviebrock\EloquentSluggable\Services\SlugService;
use App\Http\Controllers\Controller;
use App\Models\ProductStock;
use App\Models\Store;
use App\Models\Taxe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Modules\Inventory\Models\Brand;
use Modules\Inventory\Models\Category;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\ProductVariant;
use Modules\Inventory\Models\ProductVariantOption;
use Modules\Inventory\Models\ProductVariantType;
use Modules\Inventory\Models\Unit;
use Modules\Inventory\Models\Warrantie;
use Modules\POS\Models\SaleItem;

class ProductController extends Controller
{
    
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

        $products = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return view('inventory::produk._produk_table', ['produk' => $products])->render();
        }

        return view('inventory::produk.index', [
            'produk'    => $products,
            'kategoris' => Category::where('status', 1)
                ->whereNull('parent_id')          // hanya parent untuk filter
                ->whereHas('products')
                ->orderBy('name')->get(),
        ]);
    }

   
    public function create()
    {
        return view('inventory::produk.create', [
            'kategoris' => Category::where('status', 1)
                ->whereNull('parent_id')
                ->with(['children' => fn($q) => $q->where('status', 1)->orderBy('name')])
                ->orderBy('name')->get(),
            'brand'   => Brand::where('status', 1)->orderBy('name')->get(),
            'unit'    => Unit::where('status', 1)->orderBy('name')->get(),
            'garansi' => Warrantie::where('status', 1)->orderBy('name')->get(),
            'pajak'   => Taxe::orderBy('name_taxe', 'asc')->get(),
            // BARU: Kirim data toko untuk pilihan lokasi stok awal
            'stores'  => Store::query()
            ->where('is_active', 1)
            ->orderBy('name_toko', 'asc')->get(), 
        ]);
    }

    
    public function store(Request $request)
    {
        // Debug: cek format gallery input
        \Log::info("Product store - gallery debug", [
            'gallery_raw' => $request->input('gallery'),
            'gallery_sample' => collect($request->input('gallery', []))->take(1)->toArray(),
            'is_html' => collect($request->input('gallery', []))->contains(fn($v) => str_starts_with($v ?? '', '<!DOCTYPE')),
        ]);
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
            'harga_jual'    => 'nullable|numeric',
            'harga_beli'    => 'nullable|numeric',
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
            'variants.*.img_variant'         => 'nullable|string',
        ]);

       DB::transaction(function () use ($request) {
            // ---- Buat produk utama ----
            $product = Product::create([
                'name_product'  => $request->name_product,
                'slug'          => $request->slug,
                'barcode'       => $request->barcode,
                'sku'           => $request->sku,
                'description'   => $request->description,
                'specification' => $this->packSpecification($request),
                'harga_jual'    => $request->harga_jual ?? 0,
                'harga_beli'    => $request->harga_beli ?? 0,
                'stok_minimum'  => $request->stok_minimum,
                'wajib_seri'    => $request->boolean('wajib_seri'),
                'category_id'   => $request->kategori,
                'brand_id'      => $request->brand,
                'unit_id'       => $request->unit,
                'warrantie_id'  => $request->garansi,
                'taxe_id'       => $request->pajak,
                'user_id'       => Auth::id(),
            ]);

            

            // ---- 2. Simpan galeri foto ----
            $this->saveGallery($product, $request->input('gallery', []), $request->input('primary_image'));

            // ---- 3. Simpan variasi (Kirimkan store_id untuk stok variannya) ----
            if ($request->filled('variant_types')) {
                $this->saveVariants($product, $request->input('variant_types'), $request->input('variants', []));
            }
        });

        return redirect()->route('produk.index')->with('success', 'Product Baru Berhasil Ditambahkan.');
    }

    
    public function show(Product $produk)
    {
        return view('inventory::produk.show', [
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

   
    public function edit(Product $produk)
    {
        return view('inventory::produk.edit', [
            'produk'   => $produk->load([
                'images',
                'variantTypes.options',
                'variants.options.variantType',
                'stocks' // BARU: Load relasi stok agar bisa ditampilkan
            ]),
            'kategoris' => Category::where('status', 1)->whereNull('parent_id')
                ->with(['children' => fn($q) => $q->where('status', 1)->orderBy('name')])
                ->orderBy('name')->get(),
            'brands'    => Brand::where('status', 1)->orderBy('name')->get(),
            'units'     => Unit::where('status', 1)->orderBy('name')->get(),
            'warranties'=> Warrantie::where('status', 1)->orderBy('name')->get(),
            'pajak'     => Taxe::orderBy('name_taxe', 'asc')->get(),
            // BARU: Kirim data toko
            'stores'    => Store::query()
                ->where('is_active', 1)
                ->orderBy('name_toko', 'asc')
                ->get(),
        ]);
    }

    
    public function update(Request $request, Product $produk)
    {

            // Di ProductController::store(), sebelum DB::transaction()
        \Log::info("Product store debug", [
            'gallery_input' => $request->input('gallery'),
            'primary_image' => $request->input('primary_image'),
            'variant_types_count' => count($request->input('variant_types', [])),
            'variants_count' => count($request->input('variants', [])),
        ]);
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
            'harga_jual'    => 'nullable|numeric',
            'harga_beli'    => 'nullable|numeric',
            'garansi'       => 'nullable|exists:warranties,id',
            'stok_minimum'  => 'required|integer',
            'pajak'         => 'nullable|exists:taxes,id',
            'wajib_seri'    => 'nullable|boolean',

            // Galeri
            'gallery'         => 'nullable|array',
            'gallery.*'       => 'string',
            'primary_image'   => 'nullable|string',
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
                function ($attribute, $value, $fail) use ($produk) {
                    // Cukup pastikan SKU tidak dipakai oleh PRODUK LAIN
                    $exists = ProductVariant::where('sku', $value)
                        ->where('product_id', '!=', $produk->id)
                        ->exists();
                        
                    if ($exists) {
                        $fail("SKU variasi sudah digunakan oleh produk lain.");
                    }
                }
            ],
            'variants.*.barcode'    => 'nullable|string',
            'variants.*.harga_jual' => 'required_with:variants|numeric',
            'variants.*.harga_beli' => 'required_with:variants|numeric',
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
                'specification' => $this->packSpecification($request),
                'harga_jual'    => $request->harga_jual ?? 0,
                'harga_beli'    => $request->harga_beli ?? 0,
                'stok_minimum'  => $request->stok_minimum,
                'wajib_seri'    => $request->boolean('wajib_seri'),
                'category_id'   => $request->kategori,
                'brand_id'      => $request->brand,
                'unit_id'       => $request->unit,
                'warrantie_id'  => $request->garansi,
                'taxe_id'       => $request->pajak,
                'user_id'       => Auth::id(),
            ]);

                       // ---- Update galeri foto ----
            $keepIds = $request->input('existing_images', []);
            // Hapus foto lama yang tidak di-keep
            $produk->images()->whereNotIn('id', $keepIds)->each(function ($img) {
                Storage::disk('r2')->delete($img->path);
                $img->delete();
            });
            // Tambah foto baru
            $this->saveGallery($produk, $request->input('gallery', []), $request->input('primary_image'));

            
            // ---- Update variasi ----
            if ($request->filled('variant_types')) {
                // Panggil langsung saveVariants, logika update/create/delete ada di dalamnya
                $this->saveVariants($produk, $request->input('variant_types'), $request->input('variants', []));
            } else {
                // Jika user mematikan toggle varian sepenuhnya, barulah kita hapus semuanya
                $produk->variants()->each(function ($v) {
                    if ($v->img_variant) Storage::disk('r2')->delete($v->img_variant);
                    $v->delete();
                });
                $produk->variantTypes()->delete();
            }
        });

        return redirect()->route('produk.index')->with('success', 'Product Berhasil Diupdate.');
    }

    
    public function destroy(Product $produk)
    {
        // Hapus semua foto galeri
        $produk->images()->each(function ($img) {
            Storage::disk('r2')->delete($img->path);
        });
        // Hapus foto variasi
        $produk->variants()->each(function ($v) {
            if ($v->img_variant) Storage::disk('r2')->delete($v->img_variant);
        });
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Product Berhasil Dihapus.');
    }

    /**
 * Pack specification arrays into JSON string
 * Format: [{"key":"Warna","value":"Merah"},{"key":"Ukuran","value":"XL"}]
 */
    private function packSpecification(Request $request): ?string
    {
        $keys = $request->input('spec_keys', []);
        $values = $request->input('spec_values', []);
        
        // Jika tidak ada spec, return null
        if (empty($keys) || empty($values) || count($keys) !== count($values)) {
            return null;
        }
        
        $specs = [];
        foreach ($keys as $index => $key) {
            $key = trim($key);
            $value = trim($values[$index] ?? '');
            
            // Skip jika key atau value kosong
            if ($key === '' || $value === '') {
                continue;
            }
            
            $specs[] = [
                'key'   => $key,
                'value' => $value
            ];
        }
        
        return !empty($specs) ? json_encode($specs, JSON_UNESCAPED_UNICODE) : null;
    }

    // -------------------------------------------------------
    // HELPER PRIVATE: simpan galeri
    // -------------------------------------------------------
    private function saveGallery(Product $product, array $galleryPaths, ?string $primaryPath): void
    {
        $order = $product->images()->max('sort_order') + 1;
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        
        $allPaths = collect($galleryPaths);
        if ($primaryPath && !$allPaths->contains($primaryPath)) {
            $allPaths->prepend($primaryPath);
        }
        
        // ✅ SATU LOOP SAJA
        foreach ($allPaths as $tmpPath) {
            if (!$tmpPath || !str_starts_with($tmpPath, 'tmp/')) {
                \Log::warning("Gallery skip - invalid tmpPath: $tmpPath");
                continue;
            }
            
            if (!Storage::disk('r2')->exists($tmpPath)) {
                \Log::error("Gallery file not found: $tmpPath");
                continue;
            }
            
            $newPath = 'produk/gallery/' . basename($tmpPath);
            $dir = dirname($newPath);
            
            if (!Storage::disk('r2')->exists($dir)) {
                Storage::disk('r2')->makeDirectory($dir);
            }
            
            $moved = Storage::disk('r2')->move($tmpPath, $newPath);
            
            if (!$moved) {
                \Log::error("Failed to move gallery file: $tmpPath -> $newPath");
                continue;
            }
            
            \Log::info("Gallery file moved successfully: $newPath");
            
            $isPrimary = (!$hasPrimary && $tmpPath === $primaryPath) 
                    || (!$hasPrimary && $order === 1);
            
            $product->images()->create([
                'path'       => $newPath,
                'is_primary' => $isPrimary,
                'sort_order' => $order++,
            ]);
            
            if ($isPrimary) $hasPrimary = true;
        }
        
        // Fallback: jika belum ada primary, set yang pertama
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
        // 1. Buat ulang variant types & options
        // Tidak masalah dihapus, karena ID Varian utamanya (ProductVariant) akan kita pertahankan
        $product->variantTypes()->delete(); 

        $optionMap = [];
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
        
        // Array untuk melacak ID varian mana saja yang masih dipakai
        $keptVariantIds = [];

        // 2. Loop kombinasi variasi dari form
        foreach ($variantCombinations as $varData) {
            $imgPath = null;
            $tmpImg = $varData['img_variant'] ?? null;
            
            if ($tmpImg) {
                if (str_starts_with($tmpImg, 'tmp/') && Storage::disk('r2')->exists($tmpImg)) {
                    $imgPath = 'produk/variants/' . basename($tmpImg);
                    Storage::disk('r2')->move($tmpImg, $imgPath);
                } else {
                    $imgPath = $tmpImg;
                }
            }
            
            // CEK APAKAH INI VARIAN LAMA (Diedit) ATAU BARU (Ditambah)
            if (isset($varData['id']) && $varData['id']) {
                $variant = ProductVariant::find($varData['id']);
                
                // Hapus file gambar lama jika user mengunggah gambar baru
                if ($imgPath && $variant->img_variant && $imgPath !== $variant->img_variant) {
                    Storage::disk('r2')->delete($variant->img_variant);
                }

                // Lakukan UPDATE, bukan CREATE, agar ID tidak berubah dan Stok tetap aman
                $variant->update([
                    'sku'           => $varData['sku'],
                    'barcode'       => $varData['barcode'] ?? null,
                    'harga_jual'    => $varData['harga_jual'],
                    'harga_beli'    => $varData['harga_beli'],
                    'img_variant'   => $imgPath ?? $variant->img_variant, 
                ]);
            } else {
                // Buat varian baru karena tidak ada ID (biasanya karena user klik "Generate" lagi)
                $variant = ProductVariant::create([
                    'product_id'    => $product->id,
                    'sku'           => $varData['sku'],
                    'barcode'       => $varData['barcode'] ?? null,
                    'harga_jual'    => $varData['harga_jual'],
                    'harga_beli'    => $varData['harga_beli'],
                    'img_variant'   => $imgPath,
                    'is_active'     => true,
                ]);
            }
            
            $keptVariantIds[] = $variant->id;

            // Bersihkan relasi opsi pivot yang lama, lalu pasang yang baru
            $variant->options()->detach();

            if (!empty($varData['option_ids'])) {
                $idsToAttach = [];
                foreach ($varData['option_ids'] as $optValue) {
                    foreach ($optionMap as $typeName => $options) {
                        if (isset($options[$optValue])) {
                            $idsToAttach[] = $options[$optValue];
                            break; 
                        }
                    }
                }
                if (!empty($idsToAttach)) {
                    $variant->options()->attach($idsToAttach);
                }
            }
        }

        // 3. Hapus HANYA varian yang benar-benar dibuang oleh user dari form
        $product->variants()->whereNotIn('id', $keptVariantIds)->each(function ($v) {
            if ($v->img_variant) {
                Storage::disk('r2')->delete($v->img_variant);
            }
            $v->delete(); // Ini baru aman dihapus beserta stoknya, karena memang sengaja di-remove user
        });
    }

    // -------------------------------------------------------
    // UPLOAD (FilePond) — sama seperti sebelumnya
    // -------------------------------------------------------
    public function upload(Request $request)
    {
        try {
            // 1. Ambil semua file dari request (menghindari error nama key tidak cocok)
            $files = $request->allFiles();
            
            if (empty($files)) {
                return response()->json(['error' => 'Tidak ada file yang diunggah.'], 400);
            }

            // 2. Ambil file pertama terlepas dari nama input-nya ('file' atau 'gallery_files')
            $fileKey = array_key_first($files);
            $uploadedFile = $files[$fileKey];

            // Handle jika array dikirimkan (misal: name="gallery_files[]")
            if (is_array($uploadedFile)) {
                $uploadedFile = $uploadedFile[0];
            }

            // 3. Validasi ekstensi dan ukuran secara manual agar lebih aman
            $extension = strtolower($uploadedFile->getClientOriginalExtension());
            $validExtensions = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            
            if (!in_array($extension, $validExtensions)) {
                return response()->json(['error' => 'Format file tidak didukung. Gunakan JPG, PNG, WEBP, atau SVG.'], 422);
            }
            
            if ($uploadedFile->getSize() > 2048 * 1024) { // Maksimal 2MB
                return response()->json(['error' => 'Ukuran file maksimal 2MB.'], 422);
            }

            // 4. Simpan ke storage 'tmp'
            $path = $uploadedFile->store('tmp', 'r2');
            
            if ($path) {
                // 5. Kembalikan plain text agar serverId terbaca benar oleh FilePond
                return response($path, 200)->header('Content-Type', 'text/plain');
            }
            
            return response()->json(['error' => 'Gagal menyimpan file ke server.'], 500);
            
        } catch (\Exception $e) {
            \Log::error("Upload error: " . $e->getMessage());
            return response()->json(['error' => 'Sistem Error: ' . $e->getMessage()], 500);
        }
    }

    public function revert(Request $request)
    {
        $filePath = $request->getContent();
        if ($filePath && Storage::disk('r2')->exists($filePath)) {
            Storage::disk('r2')->delete($filePath);
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
        
        // Ambil ID Toko dari user yang login (sesuai logika Anda di cekStock)
        $storeId = Auth::user()->employee->store_id ?? null;

        // Load relasi pajak, varian, dan opsi variannya
        $query = Product::with(['pajak', 'primaryImage', 'variants' => function($q) {
                // Pastikan memuat opsi untuk membentuk nama varian (misal: "Merah / XL")
                $q->where('is_active', 1)->with('options'); 
            }])
            ->when($search, function ($q, $search) {
                $q->where(function($subQ) use ($search) {
                    $subQ->where('name_product', 'like', "%{$search}%")
                         ->orWhere('sku', 'like', "%{$search}%")
                         ->orWhere('barcode', 'like', "%{$search}%")
                         ->orWhereHas('variants', function($qv) use ($search) {
                             $qv->where('sku', 'like', "%{$search}%")
                                ->orWhere('barcode', 'like', "%{$search}%");
                         });
                });
            })
            ->when($request->boolean('wajib_seri'), function($q) {
                $q->where('wajib_seri', true);
            })
            ->latest(); // Gunakan latest, jangan orderBy qty karena qty ada di tabel product_stocks

        $products = $query->paginate(15);

        $formattedData = [];

        foreach ($products as $product) {
            // Skenario 1: Produk memiliki varian
            if ($product->variants && $product->variants->count() > 0) {
                foreach ($product->variants as $variant) {
                    
                    // Bentuk nama varian dari opsi (misal: "Hitam / XL")
                    $variantOptions = [];
                    if ($variant->relationLoaded('options') && $variant->options->count() > 0) {
                        foreach ($variant->options as $opt) {
                            $variantOptions[] = $opt->value;
                        }
                    }
                    $variantName = !empty($variantOptions) ? implode(' / ', $variantOptions) : "SKU: " . $variant->sku;
                    
                    // Hitung stok varian spesifik di toko saat ini
                    $stockQuery = ProductStock::query()->where('product_id', $product->id)
                                    ->where('product_variant_id', $variant->id);
                    if ($storeId) {
                        $stockQuery->where('store_id', $storeId);
                    }
                    $stokVarian = $stockQuery->sum('qty');

                    $formattedData[] = [
                        'id' => $product->id, // ID Produk Induk
                        'variant_id' => $variant->id,
                        'name_product' => $product->name_product,
                        'variant_name' => $variantName, 
                        'sku' => $variant->sku,
                        'qty' => $stokVarian,
                        'harga_beli' => $variant->harga_beli,
                        'harga_jual' => $variant->harga_jual,
                        // Gunakan gambar varian, jika tidak ada fallback ke primary image produk
                        'img_produk' => $variant->img_variant ?? $product->primaryImage->path ?? null,
                        'taxe_id' => $product->taxe_id,
                        'pajak' => $product->pajak ? ['rate' => $product->pajak->rate] : null
                    ];
                }
            } 
            // Skenario 2: Produk Simple (Tanpa Varian)
            else {
                 // Hitung stok produk induk (dimana product_variant_id adalah null)
                $stockQuery = ProductStock::query()
                ->where('product_id', $product->id)
                ->where('product_variant_id', '=', null);

                 if ($storeId) {
                     $stockQuery->where('store_id', $storeId);
                 }
                 $stokProduk = $stockQuery->sum('qty');

                $formattedData[] = [
                    'id' => $product->id,
                    'variant_id' => null,
                    'name_product' => $product->name_product,
                    'variant_name' => null,
                    'sku' => $product->sku,
                    'qty' => $stokProduk,
                    'harga_beli' => $product->harga_beli,
                    'harga_jual' => $product->harga_jual,
                    'img_produk' => $product->primaryImage->path ?? null,
                    'taxe_id' => $product->taxe_id,
                    'pajak' => $product->pajak ? ['rate' => $product->pajak->rate] : null
                ];
            }
        }

        // Return JSON yang sudah sesuai dengan ekspektasi Select2 di blade Anda
        return response()->json([
            'data' => $formattedData,
            'current_page' => $products->currentPage(),
            'next_page_url' => $products->nextPageUrl(),
        ]);
    }

    public function cekStock(Request $request)
    {
        $productId = $request->query('id');
        $storeId = Auth::user()->employee->store_id ?? null; // Sesuaikan dengan auth user
        
        $query = ProductStock::query()->where('product_id', $productId);
        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        
        $totalStok = $query->sum('qty');
        return response()->json($totalStok);
    }

    public function getByBarcode(String $barcode)
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
        $storeId = Auth::user()->employee->store_id ?? null;
    
        $lowStockProducts = Product::select('products.*')
            ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->where('product_stocks.product_variant_id', null) // Produk utama
            ->when($storeId, fn($q) => $q->where('product_stocks.store_id', $storeId))
            ->whereColumn('product_stocks.qty', '<=', 'products.stok_minimum')
            ->groupBy('products.id')
            ->orderBy('product_stocks.qty', 'asc')
            ->take(5)
            ->get(['products.id', 'products.name_product', 'products.slug', 'products.stok_minimum', 'products.img_produk']);
        
        // Hitung total count dengan query terpisah
        $lowStockCount = Product::join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->where('product_stocks.product_variant_id', null)
            ->when($storeId, fn($q) => $q->where('product_stocks.store_id', $storeId))
            ->whereColumn('product_stocks.qty', '<=', 'products.stok_minimum')
            ->count();
        
        return response()->json([
            'count' => $lowStockCount,
            'products' => $lowStockProducts->map(function ($produk) {
                // ... mapping tetap sama, tapi ambil qty dari join
                return [
                    'name_product' => Str::limit($produk->name_product, 30),
                    'qty' => $produk->stocks->firstWhere('product_variant_id', null)?->qty ?? 0, // Atau hitung via subquery
                    'stok_minimum' => $produk->stok_minimum,
                    // ...
                ];
            })
        ]);
    }

    public function getUnregisteredSerialNotifications()
    {
        $subQueryLogic = fn($query) => $query->whereNotIn('status', ['Terjual', 'Hilang']);

        // 1. Ambil 5 produk untuk ditampilkan
        $productsNeedingSerials = Product::where('wajib_seri', true)
            // Hitung total kolom 'qty' dari relasi stocks
            ->withSum('stocks as total_stok', 'qty') 
            // Hitung total baris dari relasi serialNumbers dengan filter status
            ->withCount(['serialNumbers as sn_tercatat_count' => function ($query) {
                $query->whereNotIn('status', ['Terjual', 'Hilang']);
            }])
            // Bandingkan hasilnya (Gunakan HAVING karena variabel ini baru diciptakan oleh Eloquent)
            ->havingRaw('COALESCE(total_stok, 0) > sn_tercatat_count')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 2. Hitung jumlah total produknya (untuk angka badge/notif)
        $count = Product::where('wajib_seri', true)
            ->withSum('stocks as total_stok', 'qty')
            ->withCount(['serialNumbers as sn_tercatat_count' => function ($query) {
                $query->whereNotIn('status', ['Terjual', 'Hilang']);
            }])
            ->havingRaw('COALESCE(total_stok, 0) > sn_tercatat_count')
            // Gunakan get()->count() karena count() biasa kadang bentrok dengan havingRaw
            ->get()
            ->count();

        return response()->json([
            'count'    => $count,
            'products' => $productsNeedingSerials->map(function ($produk) {
                return [
                    'name_product' => \Illuminate\Support\Str::limit($produk->name_product, 30),
                    'needed'       => $produk->qty - $produk->sn_tercatat_count,
                    'img_url'      => $produk->img_produk ? asset('storage/' . $produk->img_produk) : asset('assets/img/produk.png'),
                    'url'          => route('serialNumber.index', ['produk_slug' => $produk->slug])
                ];
            })
        ]);
    }

    public function allNotifications()
    {
        // Kueri 1: Produk Stok Rendah
        $lowStockProducts = Product::select('products.*')
            ->selectSub(function ($query) {
                // Menambahkan alias total_qty agar tetap bisa dipanggil di view blade
                $query->selectRaw('COALESCE(SUM(qty), 0)')
                    ->from('product_stocks')
                    ->whereColumn('product_stocks.product_id', 'products.id');
            }, 'total_qty')
            // PERBAIKAN: Ganti havingRaw menjadi whereRaw dengan subquery langsung
            ->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM product_stocks WHERE product_stocks.product_id = products.id) <= products.stok_minimum')
            ->orderBy('total_qty', 'asc')
            ->get();

        // Kueri 2: Produk Wajib Seri yang belum didaftarkan
        $productsNeedingSerials = Product::select('products.*')
            ->where('wajib_seri', true)
            // withSum dan withCount tetap dipanggil agar propertinya bisa dipakai di blade
            ->withSum('stocks as total_stok', 'qty')
            ->withCount(['serialNumbers as sn_tercatat_count' => fn($q) => $q->whereNotIn('status', ['Terjual', 'Hilang'])])
            // PERBAIKAN: Ganti havingRaw menjadi whereRaw dengan perbandingan 2 subquery 
            ->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM product_stocks WHERE product_stocks.product_id = products.id) > (SELECT COUNT(*) FROM serial_numbers WHERE serial_numbers.product_id = products.id AND status NOT IN ("Terjual", "Hilang"))')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('content.dashboard.all', [
            'title'                  => 'Semua Notifikasi',
            'lowStockProducts'       => $lowStockProducts,
            'productsNeedingSerials' => $productsNeedingSerials,
        ]);
    }

    public function laporanStockRendah(Request $request)
    {
        // Subquery untuk tanggal penjualan terakhir (tetap sama)
        $lastSaleDateSubquery = SaleItem::select('sales.tanggal_penjualan')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereColumn('sale_items.product_id', 'products.id')
            ->where('sales.status_pembayaran', '!=', 'Dibatalkan')
            ->orderBy('sales.tanggal_penjualan', 'desc')
            ->limit(1);

        // Query utama Product
        $query = Product::with('category')
            ->select('products.*')
            // Tetap select alias total_qty agar bisa ditampilkan di blade (view)
            ->selectSub(function ($query) {
                $query->selectRaw('COALESCE(SUM(qty), 0)')
                    ->from('product_stocks')
                    ->whereColumn('product_stocks.product_id', 'products.id');
            }, 'total_qty')
            // PERBAIKAN: Ganti havingRaw menjadi whereRaw dan jalankan subquery langsung di dalamnya
            ->whereRaw('(SELECT COALESCE(SUM(qty), 0) FROM product_stocks WHERE product_stocks.product_id = products.id) <= products.stok_minimum')
            ->addSelect(['last_sale_date' => $lastSaleDateSubquery])
            ->orderBy('total_qty', 'asc');

        // Filter Pencarian
        if ($request->filled('search')) {
            $query->where('products.nama_produk', 'like', '%' . $request->search . '%');
        }
        
        // Filter Kategori
        if ($request->filled('kategori')) {
            $query->where('products.category_id', $request->kategori);
        }

        $products  = $query->paginate(15)->withQueryString();
        $kategoris = Category::where('status', 1)->orderBy('name')->get();

        return view('inventory::inventaris.stok-rendah', [
            'title'     => 'Laporan Stock Rendah',
            'products'  => $products,
            'kategoris' => $kategoris,
        ]);
    }
}
