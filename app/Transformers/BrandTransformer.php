<?php

namespace App\Transformers;

use App\Models\Brand;
use League\Fractal\TransformerAbstract;

class BrandTransformer extends TransformerAbstract
{
    public function transform(Brand $brand)
    {
        return [
            'id' => $brand->id,
            'name' => $brand->name,
            'image' => $brand->image,
        ];
    }
}