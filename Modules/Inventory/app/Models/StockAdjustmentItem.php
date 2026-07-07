<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StockAdjustmentItem extends Model
{
  protected $table = 'stock_adjustment_items';

  protected $guarded = ['id'];

  protected $casts = [
    'jumlah' => 'integer',
    'stok_sebelum' => 'integer',
    'stok_setelah' => 'integer',
  ];

  public function stokPenyesuaian(): BelongsTo
  {
    return $this->belongsTo(StockAdjustment::class, 'stock_adjustment_id');
  }

  public function produk(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  public function variant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }

  /**
   * Badge arah pergerakan, ditentukan dari tanda `jumlah` (bukan kolom terpisah lagi).
   */
  protected function arahFormatted(): Attribute
  {
    return Attribute::make(get: fn() => $this->jumlah >= 0 ? '<span class="badge badge-sm bg-label-success">Masuk</span>' : '<span class="badge badge-sm bg-label-danger">Keluar</span>');
  }

  protected function jumlahFormatted(): Attribute
  {
    return Attribute::make(get: fn() => $this->jumlah >= 0 ? '<span class="text-success fw-bold">+' . $this->jumlah . '</span>' : '<span class="text-danger fw-bold">' . $this->jumlah . '</span>');
  }
}
