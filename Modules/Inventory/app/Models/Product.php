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
use Modules\POS\Models\SaleItem;

class Product extends Model
{
    use Sluggable;

    protected $guarded = ['id'];
    protected $with = ['category', 'user', 'brand', 'unit', 'garansi'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name_product'
            ]
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
            'total_stock' => ProductStock::selectRaw('COALESCE(SUM(qty), 0)')
                ->whereColumn('product_id', 'products.id')
                ->when($storeId, fn($q) => $q->where('store_id', $storeId))
                ->whereNull('product_variant_id') // Produk utama
        ]);
    }

    // Accessor untuk format harga
    protected function hargaFormatted(): Attribute
    {
        return Attribute::make(
            get: fn() => 'Rp ' . number_format($this->harga_jual, 0, ',', '.'),
        );
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
        return $this->belongsTo(Category::class);
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
        return $this->belongsTo(Warrantie::class);
    }
    public function pajak(): BelongsTo
    {
        return $this->belongsTo(Taxe::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return $this->hasMany(SerialNumber::class, 'item_sale_id');
    }
    public function latestPurchaseItem()
    {
        return $this->hasOne(PurchaseItem::class)->latestOfMany();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
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
        $promo = $this->promotions()
            ->where('status', true)
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->first();

        if (!$promo) {
            $promo = Promotion::where('status', true)
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_berakhir', '>=', now())
                ->whereDoesntHave('products')
                ->first();
        }

        return $promo;
    }

    public function getHargaDiskonAttribute()
    {
        if (!$this->active_promotion) {
            return null;
        }

        $promo = $this->active_promotion;
        $hargaAsli = $this->harga_jual;

        if ($promo->type == 'percentage') {
            $diskon = ($hargaAsli * $promo->nilai_diskon) / 100;
            if ($promo->max_diskon && $diskon > $promo->max_diskon) {
                $diskon = $promo->max_diskon;
            }
            return $hargaAsli - $diskon;
        } elseif ($promo->type == 'fixed') {
            return $hargaAsli - $promo->nilai_diskon;
        }

        return null;
    }
}
