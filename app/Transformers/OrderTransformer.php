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
            'guide_price' => $order->guide_price,
            'pre_amount' => $order->pre_amount,
            'price' => $order->price,
            'dkfs' => $order->dkfs,
            'sfk_text' => $order->sfk_text,
            'dkje' => $order->dkje,
            'gps' => $order->gps,
            'dyf' => $order->dyf,
            'sxf' => $order->sxf,
            'xbj' => $order->xbj,
            'gzs' => $order->gzs,
            'spf' => $order->spf,
            'ccs' => $order->ccs,
            'jqx' => $order->jqx,
            'dszzrx_text' => $order->dszzrx_text,
            'dszzrx' => $order->dszzrx,
            'clssx' => $order->clssx,
            'qcdqx' => $order->qcdqx,
            'blddpsx' => $order->blddpsx,
            'sj_csryzrx_text' => $order->sj_csryzrx_text,
            'sj_csryzrx' => $order->sj_csryzrx,
            'ck_csryzrx_text' => $order->ck_csryzrx_text,
            'ck_csryzrx' => $order->ck_csryzrx,
            'bjmptyx' => $order->bjmptyx,
            'wfzddsf' => $order->wfzddsf,
            'zrssx' => $order->zrssx,
            'syxhj' => $order->syxhj,
            'syxzk' => $order->syxzk / 10,
            'zkhje' => $order->zkhje,
            'cjzj' => $order->cjzj,
            'sfk_total' => $order->sfk_total,
            'ygje' => $order->ygje,
            'ygqs' => $order->ygqs,
            'fwf' => $order->fwf,
            'total_amount' => $order->total_amount / 100,
            'remark' => $order->remark,
            'status' => $paid_text[$order->status],
            'yf' => is_null($order->yf) ? '等待平台确认订单' : $order->yf,
            'created_at' => $order->created_at->toDateString(),
            'paid_at' => $order->paid_at
        ];
    }
}