<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Modules;

class Permission extends Model
{
    public function module()
    {
        return $this->belongsTo(Modules::class, 'modules_id');
    }
}
