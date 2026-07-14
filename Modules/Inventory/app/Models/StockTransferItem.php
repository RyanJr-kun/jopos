<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StockTransferItem extends Model
{
  protected $guarded = ['id'];

  public function stockTransfer(): BelongsTo
  {
    return $this->belongsTo(StockTransfer::class);
  }

  public function produk(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  public function serialNumbers()
  {
    return $this->belongsToMany(
      SerialNumber::class,
      'stock_transfer_item_serial_number', // Nama tabel pivot
      'stock_transfer_item_id',
      'serial_number_id',
    );
  }

  public function variant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }

  /**
   * Selisih antara qty yang dikirim vs yang diterima.
   * Positif kalau ada yang kurang (hilang/rusak di jalan).
   */
  protected function selisih(): Attribute
  {
    return Attribute::make(get: fn() => is_null($this->qty_diterima) ? null : $this->qty_kirim - $this->qty_diterima);
  }

  protected function adaSelisih(): Attribute
  {
    return Attribute::make(get: fn() => !is_null($this->qty_diterima) && $this->qty_diterima !== $this->qty_kirim);
  }
}
