<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'commission_detail';

    public static $typeMap = [
        1 => '销售收益',
        2 => '管理收益',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function third()
    {
        return $this->belongsTo(Worker::class, 'third_id', 'id');
    }
}
