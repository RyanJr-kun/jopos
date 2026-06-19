<?php

namespace Modules\Inventory\Models;

use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Semua option yang membentuk kombinasi ini (misal: Merah, 8GB, 256GB)
    public function options(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariantOption::class,
            'product_variant_option_pivot',
            'product_variant_id',
            'product_variant_option_id'
        )->with('variantType');
    }

    // Helper: label kombinasi, misal "Merah / 8GB / 256GB"
    public function getLabelAttribute(): string
    {
        return $this->options->pluck('value')->join(' / ');
    }

    public function getImgUrlAttribute(): string
    {
        if ($this->img_variant) {
            return asset('storage/' . $this->img_variant);
        }
        // Fallback ke gambar utama produk
        $primary = $this->product->images()->where('is_primary', true)->first()
            ?? $this->product->images()->first();

        return $primary
            ? asset('storage/' . $primary->path)
            : asset('assets/img/produk.png');
    }

    public function stocks()
    {
        // Menghubungkan varian dengan stoknya berdasarkan 'product_variant_id'
        return $this->hasMany(ProductStock::class, 'product_variant_id', 'id');
    }
}
