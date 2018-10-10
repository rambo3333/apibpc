<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['order_no', 'user_id', 'worker_no', 'total_amount', 'remark', 'program', 'brand', 'series',
                            'cmodel', 'image', 'cmodel_id', 'guide_price', 'pre_amount', 'price', 'dkfs', 'sfk_text',
                            'sfk', 'sfk_total', 'dkje', 'ygje', 'ygqs', 'gzs', 'spf', 'ccs', 'jqx', 'gps', 'dyf', 'sxf',
                            'xbj', 'dszzrx', 'dszzrx_text', 'clssx_option', 'clssx', 'qcdqx_option', 'qcdqx',
                            'blddpsx_option', 'blddpsx', 'sj_csryzrx_option', 'sj_csryzrx', 'sj_csryzrx_text',
                            'ck_csryzrx_option', 'ck_csryzrx', 'ck_csryzrx_text', 'bjmptyx', 'wfzddsf_option', 'wfzddsf',
                            'zrssx_option', 'zrssx', 'syxhj', 'syxzk', 'zkhje', 'cjzj', 'fwf', 'yf', 'paid_at',
                            'payment_method', 'payment_no', 'status', 'refund_no', 'closed', 'contract'];

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
