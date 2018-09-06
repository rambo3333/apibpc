<?php

namespace App\Http\Controllers\Api;

use App\Models\Series;
use Illuminate\Http\Request;
use App\Transformers\SeriesTransformer;

class SeriesController extends Controller
{
    public function index(Request $request)
    {
        $series = Series::where('brand_id', $request->brand_id)->get();

        return $this->response->collection($series, new SeriesTransformer());
    }
}
