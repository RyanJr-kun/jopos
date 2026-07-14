<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Models\Product;
use Modules\POS\Models\SaleItem;

class SerialNumber extends Model
{
  protected $guarded = ['id'];

  public function produk(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  public function saleItem(): BelongsTo
  {
    return $this->belongsTo(SaleItem::class, 'item_sale_id');
  }

  public function purchase()
  {
    return $this->belongsTo(Purchase::class, 'purchase_id');
  }

  public function variant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }

  public static function getStatus()
  {
    return ['Tersedia', 'Rusak', 'Hilang'];
  }

  public function stockTransfers()
  {
    return $this->belongsToMany(StockTransfer::class, 'stock_transfer_item_serial_number', 'serial_number_id', 'stock_transfer_item_id')->withTimestamps();
  }
}
