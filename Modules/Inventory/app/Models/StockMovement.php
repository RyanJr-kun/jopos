<?php

namespace Modules\Inventory\Models;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
  protected $table = 'stock_movements';

  protected $guarded = ['id'];

  protected $casts = [
    'qty' => 'integer',
    'stok_sebelum' => 'integer',
    'stok_setelah' => 'integer',
  ];

  const TYPE_IN = 'in';
  const TYPE_OUT = 'out';
  const TYPE_ADJUSTMENT = 'adjustment';
  const TYPE_TRANSFER_IN = 'transfer_in';
  const TYPE_TRANSFER_OUT = 'transfer_out';
  const TYPE_OPNAME = 'opname';
  const TYPE_SALE = 'sale';
  const TYPE_PURCHASE = 'purchase';

  public function store(): BelongsTo
  {
    return $this->belongsTo(Store::class);
  }

  public function produk(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  public function variant(): BelongsTo
  {
    return $this->belongsTo(ProductVariant::class, 'product_variant_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Sumber referensi (polymorphic): bisa ke StockAdjustment, StockOpname,
   * StockTransfer, Sale, Purchase, dsb.
   */
  public function referensi(): MorphTo
  {
    return $this->morphTo('referensi');
  }

  /**
   * Scope untuk membatasi movement ke store tertentu.
   * Dipakai bareng permission 'view-all-store'.
   */
  public function scopeForStore($query, $storeId)
  {
    return $query->where('store_id', $storeId);
  }

  /**
   * Accessor: nama route berdasarkan tipe referensi morph.
   */
  protected function routeName(): \Illuminate\Database\Eloquent\Casts\Attribute
  {
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
      get: fn() => match ($this->type) {
        self::TYPE_PURCHASE => 'pembelian.show',
        self::TYPE_SALE => 'penjualan.show',
        self::TYPE_OPNAME => 'stok-opname.show',
        self::TYPE_ADJUSTMENT => 'stok-penyesuaian.show',
        self::TYPE_TRANSFER_IN, self::TYPE_TRANSFER_OUT => 'stok-transfer.show',
        default => null,
      },
    );
  }

  /**
   * Accessor: label teks referensi buat ditampilin di tabel histori.
   */
  protected function referensiLabel(): \Illuminate\Database\Eloquent\Casts\Attribute
  {
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
      get: function () {
        $source = $this->referensi;
        if (!$source) {
          return $this->keterangan ?: '-';
        }

        return $source->kode_penyesuaian ?? ($source->kode_opname ?? ($source->kode_transfer ?? $source->id));
      },
    );
  }
}
