<?php

    /**
     * @param $guide_price 指导价
     * @param $pre_amount 优惠额
     * @param $transaction_price 成交价
     * @param $pl 排量
     * @param $zw 座位数
     * @return mixed
     */
    function gdfy($guide_price, $pre_amount, $transaction_price, $pl, $zw)
    {
        //购置税：购车款/(1+16%) X 购置税率(10%) (当车价优惠幅度超过指导价的 8% 时，购车款 = 指导价 * 92%)
        $gck = $transaction_price; //购车款
        $current_pre_amount = intval($guide_price * 0.08);
        if ($pre_amount > $current_pre_amount) {
            $gck = intval($guide_price * 0.92);
        }
        $data['gzs'] = round(($gck / 1.16 * 0.1), 2);

        //上牌费
        $data['spf'] = config('car.spf');

        //车船税(不足一年按当年剩余月算)
        $syy = date('n');
        $syy_rate = ((12 - $syy + 1) / 12);
        $pl = intval($pl * 10);
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
        if ($zw < 6) {
            $data['jqx'] = config('car.jqx1');
        } else {
            $data['jqx'] = config('car.jqx2');
        }

        return $data;
    }

    function syx($syxzkbl, $transaction_price, $brand_id)
    {

    }