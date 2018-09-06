<?php

namespace App\Http\Controllers\Api;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Transformers\BannerTransformer;

class BannersController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', '=', 1)->orderBy('sort', 'desc')->get();

        return $this->response->collection($banners, new BannerTransformer());
    }
}
