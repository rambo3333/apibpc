<?php

namespace App\Transformers;

use App\Models\Series;
use League\Fractal\TransformerAbstract;

class SeriesTransformer extends TransformerAbstract
{
    public function transform(Series $series)
    {
        return [
            'id' => $series->id,
            'brand_id' => $series->brand_id,
            'name' => $series->name,
        ];
    }
}