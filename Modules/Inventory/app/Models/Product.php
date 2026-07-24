<?php

namespace Modules\Inventory\Models;

use App\Models\ProductStock;
use App\Models\Taxe;
use App\Models\User;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Ecommerce\Models\Promotion;
use Modules\Inventory\Models\PurchaseItem;
use Illuminate\Support\Facades\Storage;
use Modules\POS\Models\SaleItem;

class Product extends Model
{
  use Sluggable;

  protected $guarded = ['id'];
  protected $with = [];

  public function sluggable(): array
  {
    return [
      'slug' => [
        'source' => 'name_product',
      ],
    ];
  }

  public function getRouteKeyName()
  {
    return 'slug';
  }

  public function stocks()
  {
    return $this->hasMany(ProductStock::class, 'product_id');
  }

  public function scopeWithTotalStock($query, $storeId = null)
  {
    return $query->addSelect([
      'total_stock' => ProductStock::selectRaw('COALESCE(SUM(qty), 0)')->whereColumn('product_id', 'products.id')->when($storeId, fn($q) => $q->where('store_id', $storeId)),
    ]);
  }

  // Accessor untuk format harga
  protected function hargaFormatted(): Attribute
  {
    return Attribute::make(get: fn() => 'Rp ' . number_format($this->harga_jual, 0, ',', '.'));
  }

  // -------------------------------------------------------
  // RELASI LAMA (tidak diubah)
  // -------------------------------------------------------
  public function promotions()
  {
    return $this->belongsToMany(Promotion::class, 'product_promotion');
  }
  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class, 'category_id');
  }
  public function brand(): BelongsTo
  {
    return $this->belongsTo(Brand::class);
  }
  public function unit(): BelongsTo
  {
    return $this->belongsTo(Unit::class);
  }
  public function garansi(): BelongsTo
  {
    return $this->belongsTo(Warrantie::class, 'warrantie_id');
  }
  public function pajak(): BelongsTo
  {
    return $this->belongsTo(Taxe::class, 'taxe_id');
  }
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }
  public function supplier(): BelongsTo
  {
    return $this->belongsTo(Supplier::class, 'supplier_id');
  }
  public function itemSales(): HasMany
  {
    return $this->hasMany(SaleItem::class);
  }
  public function pembelianDetails(): HasMany
  {
    return $this->hasMany(PurchaseItem::class);
  }
  public function serialNumbers(): HasMany
  {
    return $this->hasMany(SerialNumber::class, 'product_id');
  }
  public function latestPurchaseItem()
  {
    return $this->hasOne(PurchaseItem::class)->latestOfMany();
  }

  /** Tipe variasi (misal: Warna, RAM, Storage) */
  public function variantTypes(): HasMany
  {
    return $this->hasMany(ProductVariantType::class)->orderBy('sort_order');
  }

  /** Semua kombinasi variasi (SKU individu) */
  public function variants(): HasMany
  {
    return $this->hasMany(ProductVariant::class);
  }

  /** Apakah produk ini punya variasi */
  public function getHasVariantsAttribute(): bool
  {
    return $this->variants()->exists();
  }

  // -------------------------------------------------------
  // PROMO (tidak diubah)
  // -------------------------------------------------------
  public function getActivePromotionAttribute()
  {
    // ── Jalur 1: Relasi sudah di-eager-load dari controller ──────────────
    // Ini yang terjadi di POS create() setelah kita tambahkan with('promotions')
    if ($this->relationLoaded('promotions')) {
      // Cari promo aktif dari collection yang sudah ada di memory
      $promo = $this->promotions->where('status', true)->filter(fn($p) => $p->tanggal_mulai <= now() && $p->tanggal_berakhir >= now())->first();

      // Fallback global promo: tidak bisa dicek dari memory karena
      // global promo tidak di-attach ke produk ini.
      // Untuk POS/listing, kita skip global promo (return null saja).
      // Untuk halaman detail produk, eager-load tidak dipakai jadi masuk Jalur 2.
      return $promo;
    }

    // ── Jalur 2: Relasi belum di-load — query normal (untuk halaman detail, dll) ──
    $promo = $this->promotions()->where('status', true)->where('tanggal_mulai', '<=', now())->where('tanggal_berakhir', '>=', now())->first();

    if (!$promo) {
      // Global promotion: berlaku untuk semua produk tanpa assignment spesifik
      $promo = Promotion::where('status', true)->where('tanggal_mulai', '<=', now())->where('tanggal_berakhir', '>=', now())->whereDoesntHave('products')->first();
    }

    return $promo;
  }

  public function getHargaDiskonAttribute()
  {
    // ── Fix: simpan ke variabel lokal — jangan panggil $this->active_promotion DUA KALI ──
    $promo = $this->active_promotion; // ← cukup sekali

    if (!$promo) {
      return null;
    }

    $hargaAsli = $this->harga_jual;

    if ($promo->type === 'percentage') {
      $diskon = ($hargaAsli * $promo->nilai_diskon) / 100;
      if ($promo->max_diskon && $diskon > $promo->max_diskon) {
        $diskon = $promo->max_diskon;
      }
      return $hargaAsli - $diskon;
    }

    if ($promo->type === 'fixed') {
      return max(0, $hargaAsli - $promo->nilai_diskon);
    }

    return null;
  }

  public function images()
  {
    return $this->hasMany(ProductImage::class)->orderBy('sort_order');
  }

  public function primaryImage()
  {
    return $this->hasOne(ProductImage::class)->where('is_primary', true);
  }

  public function getImageUrlAttribute(): string
  {
    $imagePath = $this->primaryImage?->path;

    if ($imagePath && Storage::exists($imagePath)) {
      return Storage::url($imagePath);
    }

    return asset('assets/img/produk.png');
  }
}
