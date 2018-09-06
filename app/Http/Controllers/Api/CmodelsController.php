<?php

namespace App\Http\Controllers\Api;

use App\Models\Cmodel;
use App\Models\Series;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Transformers\CmodelTransformer;

class CmodelsController extends Controller
{
    public function index(Request $request, Cmodel $cmodel)
    {
        $query = $cmodel->query();

        if ($brand_id = $request->brand_id) {
            $query->where('brand_id', $brand_id);
        }
        if ($series_id = $request->series_id) {
            $query->where('series_id', $series_id);
        }
        if ($cmodel_id = $request->cmodel_id) {
            $query->where('id', $cmodel_id);
        }
        if ($search = $request->search) {
            $query->orWhere(function($query) use ($search){
                $query->whereHas('Brand', function($query) use ($search){
                    $query->where('name', 'like', $search . '%');
                });
            })->orWhere(function($query) use ($search){
                $query->whereHas('Series', function($query) use ($search){
                    $query->where('name', 'like', $search . '%');
                });
            });
        }

        $cmodels = $query->paginate(6);
        return $this->response->paginator($cmodels, new CmodelTransformer());
    }

    public function home(Request $request)
    {
        $cmodels = Cmodel::where('is_new', '=', 1)->orWhere('is_recommend', '=', 1)->get();

        return $this->response->collection($cmodels, new CmodelTransformer());
    }
}
