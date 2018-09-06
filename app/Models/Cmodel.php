<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cmodel extends Model
{
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function series()
    {
        return $this->belongsTo(Series::class);
    }

    public function stype()
    {
        return $this->belongsTo(Stype::class);
    }
}
