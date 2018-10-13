<?php

namespace App\Transformers;

use App\Models\Order;
use App\Models\Brand;
use App\Models\Series;
use App\Models\Cmodel;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function transform(Order $order)
    {
        $paid_text = [
            'not_paid' => '待支付',
            'paid' => '已支付',
            'paid_fail' => '支付失败',
            'refund' => '退款成功',
        ];

        return [
            'id' => $order->id,
            'order_no' => $order->order_no,
            'program' => $order->program,
            'brand' => $order->brand,
            'series' => $order->series,
            'cmodel' => $order->cmodel,
            'image' => $order->image,
            'status' => $paid_text[$order->status],
            'yf' => is_null($order->yf) ? '等待平台确认订单' : $order->yf,
            'created_at' => $order->created_at->toDateString(),
            'paid_at' => $order->paid_at
        ];
    }
}