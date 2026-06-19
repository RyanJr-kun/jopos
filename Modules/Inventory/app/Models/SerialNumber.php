<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Models\Product;
use Modules\POS\Models\Sale;

class SerialNumber extends Model
{
    protected $guarded = ['id'];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Sale::class, 'penjualan_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public static function getStatus()
    {
        return ['Tersedia', 'Rusak', 'Hilang'];
    }
}
