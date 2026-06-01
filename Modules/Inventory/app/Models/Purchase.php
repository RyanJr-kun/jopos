<?php

namespace Modules\Inventory\Models;

use App\Models\User;
use Modules\Inventory\Models\Supplier;
use Modules\Inventory\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    protected $guarded = ['id'];
    public function getRouteKeyName()
    {
        return 'referensi';
    }
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function details(): HasMany
    { 
        return $this->hasMany(PurchaseItem::class);
    }
    // Tambahkan attribute ini
    public function getPersentaseBayarAttribute()
    {
        $total = $this->total_akhir > 0 ? $this->total_akhir : 1; 
        $persentase = round(($this->jumlah_dibayar / $total) * 100);
        return $persentase > 100 ? 100 : $persentase;
    }

    public function getIsOverdueAttribute()
    {
        if (!$this->tanggal_jatuh_tempo || $this->status_pembayaran === 'Lunas' || $this->status_pembayaran === 'Dibatalkan') {
            return false;
        }
        return \Carbon\Carbon::now()->startOfDay()->gt(\Carbon\Carbon::parse($this->tanggal_jatuh_tempo)->startOfDay());
    }

    public static function getPaymentStatus()
    {
        return [
            'Lunas',
            'Hutang',
            'Batal'
        ];
    }

    public static function getPaymentMethods()
    {
        return [
            'TUNAI',
            'TRANSFER',
            'QRIS'
        ];
    }

    public static function getStatusBarangs()
    {
        return [
            'Diterima',
            'Pre Order',
            'Retur',
            'Batal'
        ];
    }
}
