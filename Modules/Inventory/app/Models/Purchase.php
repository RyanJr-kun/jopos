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
        return $this->belongsTo(Supplier::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function details(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
