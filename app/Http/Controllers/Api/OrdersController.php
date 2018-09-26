<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\OrderRequest;
use EasyWeChat\Factory;

class OrdersController extends Controller
{
    public function store()
    {
        $app = Factory::payment(config('wechat.payment.default'));

        $user = \Auth::guard('api')->user();

        //生成订单号
        $out_trade_no = date('Ymd') . mt_rand(100000, 999999);

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
