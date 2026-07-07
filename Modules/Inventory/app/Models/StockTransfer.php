<?php

namespace Modules\Inventory\Models;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
  protected $guarded = ['id'];

  protected $casts = [
    'tanggal_kirim' => 'datetime',
    'tanggal_diterima' => 'datetime',
  ];

  const STATUS_DRAFT = 'draft';
  const STATUS_DIKIRIM = 'dikirim';
  const STATUS_DITERIMA = 'diterima';
  const STATUS_DITERIMA_SEBAGIAN = 'diterima_sebagian';
  const STATUS_DITOLAK = 'ditolak';
  const STATUS_DIBATALKAN = 'dibatalkan';

  public function getRouteKeyName()
  {
    return 'kode_transfer';
  }

  public function storeAsal(): BelongsTo
  {
    return $this->belongsTo(Store::class, 'store_asal_id');
  }

  public function storeTujuan(): BelongsTo
  {
    return $this->belongsTo(Store::class, 'store_tujuan_id');
  }

  public function userKirim(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_kirim_id');
  }

  public function userTerima(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_terima_id');
  }

  public function details(): HasMany
  {
    return $this->hasMany(StockTransferItem::class);
  }

  /**
   * Adjustment yang auto-generated kalau ada selisih pas penerimaan.
   */
  public function adjustments()
  {
    return $this->morphMany(StockAdjustment::class, 'sumber');
  }

  /**
   * Scope: transfer yang relevan buat store tertentu, baik sebagai asal maupun tujuan.
   */
  public function scopeForStore($query, $storeId)
  {
    return $query->where(function ($q) use ($storeId) {
      $q->where('store_asal_id', $storeId)->orWhere('store_tujuan_id', $storeId);
    });
  }

  /**
   * Scope: transfer yang lagi nunggu diterima oleh store tujuan tertentu.
   */
  public function scopeMenungguDiterima($query, $storeTujuanId = null)
  {
    $query->where('status', self::STATUS_DIKIRIM);

    if ($storeTujuanId) {
      $query->where('store_tujuan_id', $storeTujuanId);
    }

    return $query;
  }

  protected function statusBadge(): \Illuminate\Database\Eloquent\Casts\Attribute
  {
    return \Illuminate\Database\Eloquent\Casts\Attribute::make(
      get: fn() => match ($this->status) {
        self::STATUS_DRAFT => '<span class="badge bg-label-secondary">Draft</span>',
        self::STATUS_DIKIRIM => '<span class="badge bg-label-warning">Dikirim</span>',
        self::STATUS_DITERIMA => '<span class="badge bg-label-success">Diterima</span>',
        self::STATUS_DITERIMA_SEBAGIAN => '<span class="badge bg-label-info">Diterima Sebagian</span>',
        self::STATUS_DITOLAK => '<span class="badge bg-label-danger">Ditolak</span>',
        self::STATUS_DIBATALKAN => '<span class="badge bg-label-dark">Dibatalkan</span>',
        default => '<span class="badge bg-label-secondary">' . $this->status . '</span>',
      },
    );
  }

  public static function generateKode(): string
  {
    $prefix = 'TRF-' . now()->format('Ymd') . '-';
    $last = static::where('kode_transfer', 'like', $prefix . '%')
      ->orderByDesc('kode_transfer')
      ->value('kode_transfer');

    $urutan = $last ? ((int) substr($last, -4)) + 1 : 1;

    return $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);
  }
}
