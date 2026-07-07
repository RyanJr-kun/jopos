<?php

namespace Modules\Inventory\Models;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
  protected $guarded = ['id'];

  protected $casts = [
    'tanggal_opname' => 'datetime',
  ];

  public function getRouteKeyName()
  {
    return 'kode_opname';
  }

  public function details(): HasMany
  {
    return $this->hasMany(StockOpnameItem::class);
  }

  public function store(): BelongsTo
  {
    return $this->belongsTo(Store::class);
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Adjustment yang auto-generated dari opname ini (kalau ada selisih).
   */
  public function adjustments()
  {
    return $this->morphMany(StockAdjustment::class, 'sumber');
  }

  public function scopeForStore($query, $storeId)
  {
    return $query->where('store_id', $storeId);
  }

  public static function getStatus(): array
  {
    return ['Proses', 'Selesai', 'Batal'];
  }

  public static function generateKode(): string
  {
    $prefix = 'OPN-' . now()->format('Ymd') . '-';
    $last = static::where('kode_opname', 'like', $prefix . '%')
      ->orderByDesc('kode_opname')
      ->value('kode_opname');

    $urutan = $last ? ((int) substr($last, -4)) + 1 : 1;

    return $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);
  }
}
