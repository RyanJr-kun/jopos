<?php
// ============================================================
// FILE: app/Models/ProductVariantOption.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariantOption extends Model
{
    protected $guarded = ['id'];

    public function variantType(): BelongsTo
    {
        return $this->belongsTo(ProductVariantType::class, 'variant_type_id');
    }

    // Relasi ke kombinasi variant yang menggunakan option ini
    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_option_pivot',
            'product_variant_option_id',
            'product_variant_id'
        );
    }
}
