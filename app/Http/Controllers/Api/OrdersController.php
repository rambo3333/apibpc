<?php

namespace App\Http\Controllers\Api;

use App\Models\Cmodel;
use Illuminate\Http\Request;
use App\Http\Requests\Api\OrderRequest;
use EasyWeChat\Factory;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $cmodel_id = $request->cmodel_id;
        $program_id = $request->program_id;

        $cmodel = Cmodel::find($cmodel_id);

        //系统指导价
        $guide_price = $cmodel->guide_price;


        $program_text_arr = [
            1 => '全款',
            2 => '贷款',
            3 => '低首付零首付'
        ];



        switch ($program_id) {
            case 1:

                break;
        }

        $app = Factory::payment(config('wechat.payment.default'));

        $user = \Auth::guard('api')->user();

        //请求微信统一下单接口
        $result = $app->order->unify([
            'body' => '预付定金',
            'out_trade_no' => $out_trade_no,
            'total_fee' => 1,
            'notify_url' => 'https://pay.weixin.qq.com/wxpay/notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'openid' => $user->weapp_openid,
        ]);

        return $this->response->array($result);
    }
}
