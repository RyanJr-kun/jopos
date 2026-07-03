<?php

namespace Modules\Inventory\Models;

use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function imgVariantUrl(): Attribute
{
    return Attribute::make(
        get: function () {
            if ($this->img_variant) {
                // Sesuaikan path storage Anda
                return asset('storage/img/variants/' . $this->img_variant); 
            }
            // Gambar default jika varian tidak punya gambar
            return asset('assets/img/produk.png');
        }
    );
}

    public function stocks()
    {
        return $this->hasMany(ProductStock::class, 'product_variant_id');
    }

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class, 'product_variant_id');
    }
}
