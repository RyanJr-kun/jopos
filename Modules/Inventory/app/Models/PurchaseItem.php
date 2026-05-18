<?php

namespace Modules\Inventory\Models;

use App\Models\Taxe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function produk(): BelongsTo
    {
        // Tambahkan 'product_id'
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function pajak(): BelongsTo
    {
        return $this->belongsTo(Taxe::class, 'taxe_id');
    }

    public function varian(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
