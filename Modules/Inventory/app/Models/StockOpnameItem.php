<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StockOpnameItem extends Model
{
  protected $guarded = ['id'];

  public function stokOpname(): BelongsTo
  {
    return $this->belongsTo(StockOpname::class);
  }

  public function produk(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  public function variant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }

  protected function selisihFormatted(): Attribute
  {
    return Attribute::make(
      get: fn() => match (true) {
        $this->selisih > 0 => '<span class="text-success fw-bold">+' . $this->selisih . '</span>',
        $this->selisih < 0 => '<span class="text-danger fw-bold">' . $this->selisih . '</span>',
        default => '<span class="text-muted">0</span>',
      },
    );
  }
}
