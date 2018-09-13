<?php

namespace App\Http\Controllers\Api;

use App\Models\Cmodel;
use App\Models\Series;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Transformers\CmodelTransformer;

class CmodelsController extends Controller
{
    protected $dszzrx;
    protected $csryzrx;

    public function __construct()
    {
        $this->dszzrx = [
            ['id' => 1, 'name' => '50万', 'value' => config('car.dszzrx1')],
            ['id' => 2, 'name' => '100万', 'value' => config('car.dszzrx2')],
        ];

        $this->csryzrx = [
            ['id' => 1, 'name' => '1万/座', 'value' => config('car.csryzrx1')],
            ['id' => 2, 'name' => '2万/座', 'value' => config('car.csryzrx2')],
            ['id' => 3, 'name' => '5万/座', 'value' => config('car.csryzrx3')],
        ];
    }

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
        $data['gzs'] = floor($gck / 1.16 * 0.1);

        //上牌费
        $data['spf'] = config('car.spf');

        //车船税(不足一年按当年剩余月算)
        $syy = date('n');
        $syy_rate = ((12 - $syy + 1) / 12);
        $pl = intval($cmodel->pl * 10);
        if ($pl <= 10) {
            $ccs = config('car.ccs1');
        } elseif (10 < $pl && $pl <= 16) {
            $ccs = config('car.ccs2');
        } elseif(16 < $pl && $pl <= 20) {
            $ccs = config('car.ccs3');
        } elseif (20 < $pl && $pl <= 25) {
            $ccs = config('car.ccs4');
        } elseif (25 < $pl && $pl <= 30) {
            $ccs = config('car.ccs5');
        } elseif (30 < $pl && $pl <= 40) {
            $ccs = config('car.ccs6');
        } elseif (40 < $pl) {
            $ccs = config('car.ccs7');
        }
        $data['ccs'] = $ccs * $syy_rate;

        //交强险
        if ($cmodel->zw < 6) {
            $data['jqx'] = config('car.jqx1');
        } else {
            $data['jqx'] = config('car.jqx2');
        }

        //车上人员责任险计算
        foreach ($this->csryzrx as $key => $item) {
            $this->csryzrx[$key]['value'] = $item['value'] * $cmodel->zw;
        }

        //商业险（全款）
        if ($program_id == 1) {
            $data['one_dszzrx'] = $this->dszzrx;
            $data['one_dszzrx_status'] = $cmodel->one_dszzrx_status;
            $data['one_dszzrx_default'] = $cmodel->one_dszzrx_default;
            $data['one_clssx'] = $cmodel->one_clssx;
            $data['one_clssx_status'] = $cmodel->one_clssx_status;
            $data['one_qcdqx'] = $cmodel->one_qcdqx;
            $data['one_qcdqx_status'] = $cmodel->one_qcdqx_status;
            $data['one_blddpsx'] = $cmodel->one_blddpsx;
            $data['one_blddpsx_status'] = $cmodel->one_blddpsx_status;
            $data['one_csryzrx'] = $this->csryzrx;
            $data['one_csryzrx_status'] = $cmodel->one_csryzrx_status;
            $data['one_csryzrx_default'] = $cmodel->one_csryzrx_default;
            $data['one_bjmptyx'] = $cmodel->one_bjmptyx;
            $data['one_bjmptyx_status'] = $cmodel->one_bjmptyx_status;
            $data['one_wfzddsf'] = $cmodel->one_wfzddsf;
            $data['one_wfzddsf_status'] = $cmodel->one_wfzddsf_status;
            $data['one_zrssx'] = $cmodel->one_zrssx;
            $data['one_zrssx_status'] = $cmodel->one_zrssx_status;
            $data['one_clsshuix'] = $cmodel->one_clsshuix;
            $data['one_clsshuix_status'] = $cmodel->one_clsshuix_status;
        } elseif ($program_id == 2) {
            //抵押费
            $data['dyf'] = config('car.dyf');

            $data['two_dszzrx'] = $this->dszzrx;
            $data['two_dszzrx_status'] = $cmodel->two_dszzrx_status;
            $data['two_dszzrx_default'] = $cmodel->two_dszzrx_default;
            $data['two_clssx'] = $cmodel->two_clssx;
            $data['two_clssx_status'] = $cmodel->two_clssx_status;
            $data['two_qcdqx'] = $cmodel->two_qcdqx;
            $data['two_qcdqx_status'] = $cmodel->two_qcdqx_status;
            $data['two_blddpsx'] = $cmodel->two_blddpsx;
            $data['two_blddpsx_status'] = $cmodel->two_blddpsx_status;
            $data['two_csryzrx'] = $this->csryzrx;
            $data['two_csryzrx_status'] = $cmodel->two_csryzrx_status;
            $data['two_csryzrx_default'] = $cmodel->two_csryzrx_default;
            $data['two_bjmptyx'] = $cmodel->two_bjmptyx;
            $data['two_bjmptyx_status'] = $cmodel->two_bjmptyx_status;
            $data['two_wfzddsf'] = $cmodel->two_wfzddsf;
            $data['two_wfzddsf_status'] = $cmodel->two_wfzddsf_status;
            $data['two_zrssx'] = $cmodel->two_zrssx;
            $data['two_zrssx_status'] = $cmodel->two_zrssx_status;
            $data['two_clsshuix'] = $cmodel->two_clsshuix;
            $data['two_clsshuix_status'] = $cmodel->two_clsshuix_status;
        } elseif ($program_id == 3) {
            //抵押费
            $data['dyf'] = config('car.dyf');

            $data['three_dszzrx'] = $this->dszzrx;
            $data['three_dszzrx_status'] = $cmodel->three_dszzrx_status;
            $data['three_dszzrx_default'] = $cmodel->three_dszzrx_default;
            $data['three_clssx'] = $cmodel->three_clssx;
            $data['three_clssx_status'] = $cmodel->three_clssx_status;
            $data['three_qcdqx'] = $cmodel->three_qcdqx;
            $data['three_qcdqx_status'] = $cmodel->three_qcdqx_status;
            $data['three_blddpsx'] = $cmodel->three_blddpsx;
            $data['three_blddpsx_status'] = $cmodel->three_blddpsx_status;
            $data['three_csryzrx'] = $this->csryzrx;
            $data['three_csryzrx_status'] = $cmodel->three_csryzrx_status;
            $data['three_csryzrx_default'] = $cmodel->three_csryzrx_default;
            $data['three_bjmptyx'] = $cmodel->three_bjmptyx;
            $data['three_bjmptyx_status'] = $cmodel->three_bjmptyx_status;
            $data['three_wfzddsf'] = $cmodel->three_wfzddsf;
            $data['three_wfzddsf_status'] = $cmodel->three_wfzddsf_status;
            $data['three_zrssx'] = $cmodel->three_zrssx;
            $data['three_zrssx_status'] = $cmodel->three_zrssx_status;
            $data['three_clsshuix'] = $cmodel->three_clsshuix;
            $data['three_clsshuix_status'] = $cmodel->three_clsshuix_status;
        }

        return $this->response->array($data);
    }
}
