<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_no', 'user_id', 'worker_no', 'total_amount', 'remark', 'program', 'brand', 'series',
                            'cmodel', 'image', 'cmodel_id', 'guide_price', 'pre_amount', 'price', 'dkfs', 'sfk_type',
                            'sfk', 'sfk_total', 'ygje', 'ygqs', 'gzs', 'spf', 'ccs', 'jqx', 'gps', 'dyf', 'sxf', 'xbj',
                            'dszzrx_id', 'dszzrx', 'dszzrx_bzj', 'clssx', 'qcdqx', 'blddpsx', 'sj_csryzrx_id',
                            'sj_csryzrx', 'sj_csryzrx_bzj', 'ck_csryzrx_id', 'ck_csryzrx', 'ck_csryzrx_bzj', 'bjmptyx',
                            'wfzddsf', 'zrssx', 'syxhj', 'syxzk', 'zkhje', 'cjzj', 'fwf', 'paid_at', 'payment_method',
                            'payment_no', 'status', 'refund_status', 'refund_no', 'closed'];

    protected static function boot()
    {
        parent::boot();
        // 监听模型创建事件，在写入数据库之前触发
        static::creating(function ($model) {
            // 如果模型的 order_no 字段为空
            if (!$model->order_no) {
                // 调用 findAvailableNo 生成订单流水号
                $model->order_no = static::findAvailableNo();
                // 如果生成失败，则终止创建订单
                if (!$model->order_no) {
                    return false;
                }
            }
        });
    }

    public static function findAvailableNo()
    {
        // 订单流水号前缀
        $prefix = date('YmdHis');
        for ($i = 0; $i < 10; $i++) {
            // 随机生成 6 位的数字
            $order_no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            // 判断是否已经存在
            if (!static::query()->where('order_no', $order_no)->exists()) {
                return $order_no;
            }
        }
        \Log::warning('find order no failed');

        return false;
    }

    public function cmodel()
    {
        return $this->belongsTo(Cmodel::class);
    }
}
