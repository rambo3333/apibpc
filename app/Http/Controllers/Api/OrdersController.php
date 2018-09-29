<?php

namespace App\Http\Controllers\Api;

use App\Models\Cmodel;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Requests\Api\OrderRequest;
use EasyWeChat\Factory;

class OrdersController extends Controller
{
    public function store(OrderRequest $request)
    {
        $cmodel_id = $request->cmodel_id;
        $program_id = $request->program_id;
        $user = \Auth::guard('api')->user();

        $program_text_arr = [
            1 => '全款',
            2 => '贷款',
            3 => '低首付零首付'
        ];

        $cmodel = Cmodel::find($cmodel_id);

        $price = $cmodel->guide_price - $cmodel->pre_amount; //成交价

        //固定费用
        $data = gdfy($cmodel->guide_price, $cmodel->pre_amount, $price, $cmodel->pl, $cmodel->zw);
        //用户ID
        $data['user_id'] = $user->id;
        //业务员编号
        $data['worker_no'] = $request->worker_no;
        //定金
        $data['total_amount'] = config('car.dj');
        //备注
        $data['remark'] = $request->remark;
        //购车方案
        $data['program'] = $program_text_arr[$program_id];
        //车型ID
        $data['cmodel_id'] = $request->cmodel_id;
        //指导价
        $data['guide_price'] = $cmodel->guide_price;
        //优惠额度
        $data['pre_amount'] = $cmodel->pre_amount;
        //成交价
        $data['price'] = $price;
        //


        /*switch ($program_id) {
            case 1:

                break;
        }*/

        //创建一个的订单
        $order = new Order($data);

        //写入数据库
        $order->save();

        $app = Factory::payment(config('wechat.payment.default'));

        //请求微信统一下单接口
        $result = $app->order->unify([
            'body' => '预付定金',
            'out_trade_no' => $order->order_no,
            'total_fee' => 1,
            'notify_url' => 'https://api.bpche.com.cn/wxpay/notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'trade_type' => 'JSAPI',
            'openid' => $user->weapp_openid,
        ]);

        return $this->response->array($result);
    }
}
