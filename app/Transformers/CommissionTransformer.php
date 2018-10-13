<?php

namespace App\Transformers;

use App\Models\Commission;
use League\Fractal\TransformerAbstract;

class CommissionTransformer extends TransformerAbstract
{
    public function transform(Commission $commission)
    {
        return [
            'id' => $commission->id,
            'type' => Commission::$typeMap[$commission->type],
            'thrid_name' => $commission->third['name'],
            'order_no' => $commission->order_no,
            'user' => $commission->user['mobile'],
            'level_name' => $commission->level_name,
            'commission_rate' => $commission->commission_rate,
            'commission' => $commission->commission,
            'created_at' => $commission->created_at->toDateString(),
        ];
    }
}