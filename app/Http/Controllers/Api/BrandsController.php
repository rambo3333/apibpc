<?php

namespace App\Http\Controllers\Api;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Transformers\BrandTransformer;

class BrandsController extends Controller
{
    public function index()
    {
        $brands = Brand::select('id', 'name', 'image')->get();

        return $this->response->collection($brands, new BrandTransformer());
    }

    public function recommend()
    {
        $brands = Brand::where('is_recommend', 2)->orderBy('sort', 'desc')->get();

        return $this->response->collection($brands, new BrandTransformer());
    }
}
