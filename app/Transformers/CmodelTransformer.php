<?php

namespace App\Transformers;

use App\Models\Cmodel;
use League\Fractal\TransformerAbstract;

class CmodelTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['brand', 'series'];

    public function transform(Cmodel $cmodel)
    {
        return [
            'id' => $cmodel->id,
            'brand_id' => $cmodel->brand_id,
            'series_id' => $cmodel->series_id,
            'name' => $cmodel->name,
            'image' => $cmodel->image,
            'guide_price' => $cmodel->guide_price,
            'pre_amount' => $cmodel->pre_amount,
            'brand' => $cmodel->brand['name'],
            'series' => $cmodel->series['name'],
        ];
    }

    public function includeBrand(Cmodel $cmodel)
    {
        return $this->item($cmodel->brand, new BrandTransformer());
    }

    public function includeSeries(Cmodel $cmodel)
    {
        return $this->item($cmodel->series, new SeriesTransformer());
    }
}