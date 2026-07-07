<?php

namespace Modules\Inventory\Models;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockAdjustment extends Model
{
  protected $guarded = ['id'];

  protected $casts = [
    'tanggal_penyesuaian' => 'datetime',
  ];

  public function getRouteKeyName()
  {
    return 'kode_penyesuaian';
  }

  public function store(): BelongsTo
  {
    return $this->belongsTo(Store::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function details(): HasMany
  {
    return $this->hasMany(StockAdjustmentItem::class);
  }

  /**
   * Kalau adjustment ini auto-generated dari sumber lain
   * (misal StockOpname atau StockTransfer yang ada selisih).
   */
  public function sumber(): MorphTo
  {
    return $this->morphTo('sumber');
  }

  public function scopeForStore($query, $storeId)
  {
    return $query->where('store_id', $storeId);
  }

  public static function generateKode(): string
  {
    $prefix = 'ADJ-' . now()->format('Ymd') . '-';
    $last = static::where('kode_penyesuaian', 'like', $prefix . '%')
      ->orderByDesc('kode_penyesuaian')
      ->value('kode_penyesuaian');

    $urutan = $last ? ((int) substr($last, -4)) + 1 : 1;

    return $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);
  }
}
