<?php

namespace App\Models;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    public function permission()
    {
        return $this->hasMany(Permission::class);
    }
}
