<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    public function notify()
    {
        $app = Factory::payment(config('wechat.payment.default'));

        $response = $app->handlePaidNotify(function($message, $fail){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = Order::where('order_no', ($message['out_trade_no']))->first();

            file_put_contents('nofify111.txt', json_encode($order));

            if (!$order || $order->paid_at) { // 如果订单不存在 或者 订单已经支付过了
                return true; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }

            ///////////// <- 建议在这里调用微信的【订单查询】接口查一下该笔订单的情况，确认是已经支付 /////////////

            if ($message['return_code'] === 'SUCCESS') { // return_code 表示通信状态，不代表支付状态
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $order->paid_at = Carbon::now(); // 更新支付时间为当前时间
                    $order->payment_method = 'wechat';
                    $order->payment_no = $message['transaction_id'];
                    $order->status = 'paid';

                    // 用户支付失败
                } elseif (array_get($message, 'result_code') === 'FAIL') {
                    $order->status = 'paid_fail';
                }
            } else {
                return $fail('通信失败，请稍后再通知我');
            }

            $order->save(); // 保存订单

            return true; // 返回处理完成
        });

        return $response;
    }

    public function refund()
    {
        $app = Factory::payment(config('wechat.payment.default'));

        $response = $app->handleRefundedNotify(function ($message, $reqInfo, $fail) {
            // 其中 $message['req_info'] 获取到的是加密信息
            // $reqInfo 为 message['req_info'] 解密后的信息
            // 你的业务逻辑...
            //return true; // 返回 true 告诉微信“我已处理完成”
            // 或返回错误原因 $fail('参数格式校验错误');

            // 没有找到对应的订单，原则上不可能发生，保证代码健壮性
            if(!$order = Order::where('order_no', $reqInfo['out_trade_no'])->first()) {
                return $fail('未找到对应的订单');
            }

            if ($reqInfo['refund_status'] === 'SUCCESS') {
                // 退款成功，将订单退款状态改成退款成功
                $order->update([
                    'status' => Order::STATUS_REFUND,
                    'refund_status' => Order::REFUND_STATUS_SUCCESS,
                ]);

                return true;
            } else {
                // 退款失败，将具体状态存入 extra 字段，并表退款状态改成失败
                $extra = $order->extra;
                $extra['refund_failed_code'] = $reqInfo['refund_status'];
                $order->update([
                    'refund_status' => Order::REFUND_STATUS_FAILED,
                ]);

                return $fail('退款失败');
            }
        });

        return $response;
    }
}
