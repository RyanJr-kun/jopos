<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    

    public function getUrlAttribute(): string
    {
        if (empty($this->path)) {
            return asset('assets/img/produk.png'); 
        }
        return Storage::disk('r2')->url($this->path);
    }
}
