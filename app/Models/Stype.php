<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stype extends Model
{
    public function cmodels()
    {
        return $this->hasMany(Cmodel::class, 'stype_id', 'id');
    }
}
