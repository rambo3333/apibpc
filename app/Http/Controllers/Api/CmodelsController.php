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

    public function show(Cmodel $cmodel)
    {
        return $this->response->item($cmodel, new CmodelTransformer());
    }

    public function program(Request $request)
    {
        $cmodel_id = $request->cmodel_id;
        $program_id = $request->program_id;

        $cmodel = Cmodel::find($cmodel_id);

        //系统指导价
        $data['guide_price'] = $cmodel->guide_price;
        //成交价
        $data['transaction_price'] = $data['guide_price'] - $cmodel->pre_amount;
        //购置税：购车款/(1+16%) X 购置税率(10%) (当车价优惠幅度超过指导价的 8% 时，购车款 = 指导价 * 92%)
        $gck = $data['transaction_price'];
        $pre_amount = intval($data['guide_price'] * 0.08);
        if ($cmodel->pre_amount > $pre_amount) {
            $gck = intval($data['guide_price'] * 0.92);
        }
        $data['gzs'] = $gck / 1.16 * 0.1;
        //上牌费
        $data['spf'] = config('car.spf');
        //车船税(不足一年按当年剩余月算)
        $syy = date('n');
        $ccs =



        if ($program_id == 1) {

        }
    }
}
