<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Series extends Model
{
    public function cmodels()
    {
        return $this->hasMany(Cmodel::class);
    }
}
