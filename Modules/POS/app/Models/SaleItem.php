<?php

namespace Modules\POS\Models;

use App\Models\Taxe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Inventory\Models\Product;
use Modules\Inventory\Models\ProductVariant;
use Modules\Inventory\Models\SerialNumber;

class SaleItem extends Model
{
  protected $guarded = ['id'];

  public function penjualan(): BelongsTo
  {
    return $this->belongsTo(Sale::class);
  }

  public function product(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  public function pajak(): BelongsTo
    {
        return $this->belongsTo(Taxe::class, 'taxe_id');
    }

  public function serialNumbers(): HasMany
  {
    return $this->hasMany(SerialNumber::class, 'item_sale_id');
  }
  public function varian(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }
}
