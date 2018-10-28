<?php

namespace App\Transformers;

use App\Models\Stype;
use App\Models\Cmodel;
use League\Fractal\TransformerAbstract;
use App\Transformers\CmodelTransformer;

class StypeTransformer extends TransformerAbstract
{

    public function transform(Stype $stype)
    {
        return [
            'id' => $stype->id,
            'name' => $stype->name,
            'image' => $stype->image,
            'mark_image' => $stype->mark_image,
            'cmodels' => $this->transformCmodels($stype->cmodels()->take(10)->get()),
        ];
    }

    public function transformCmodels($cmodels)
    {
        $array = [];
        foreach ($cmodels as $item){
            $array[count($array)] = (new CmodelTransformer())->transform($item);
        }
        return $array;
    }
}