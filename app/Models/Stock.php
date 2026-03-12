<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $guarded = ['id'];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
