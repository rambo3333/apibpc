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

    /**
     * @param $data
     * @param $transaction_price 购车款
     * @param $brand_id 品牌ID
     * @param $options 商业险选项
     * @param int $syxzkbl 商业险折扣
     */
    function syx(&$data, $transaction_price, $brand_id, $options, $syxzkbl = 100)
    {
        //第三者责任险综合信息
        $dszzrx = [
            ['id' => 1, 'name' => '50万', 'value' => config('car.dszzrx1')],
            ['id' => 2, 'name' => '100万', 'value' => config('car.dszzrx2')],
            ['id' => 3, 'name' => '150万', 'value' => config('car.dszzrx3')],
        ];

        //车上人员责任险综合信息（司机）
        $sj_csryzrx = [
            ['id' => 1, 'name' => '1万/座', 'value' => config('car.csryzrx_sj')],
            ['id' => 2, 'name' => '2万/座', 'value' => config('car.csryzrx_sj') * 2],
            ['id' => 3, 'name' => '5万/座', 'value' => config('car.csryzrx_sj') * 5],
        ];

        //车上人员责任险综合信息（乘客）
        $ck_csryzrx = [
            ['id' => 1, 'name' => '1万/座', 'value' => config('car.csryzrx_ck') * 4],
            ['id' => 2, 'name' => '2万/座', 'value' => config('car.csryzrx_ck') * 2 * 4],
            ['id' => 3, 'name' => '5万/座', 'value' => config('car.csryzrx_ck') * 5 * 4],
        ];

        //车辆损失险保费
        $clssx = round($transaction_price * config('car.clssx_rate'), 2);
        //全车盗抢险保费
        $qcdqx = round($transaction_price * config('car.qcdqx_rate'), 2);
        //玻璃单独破碎险
        $blddpsx = round($transaction_price * config('car.blddpsx_rate'), 2);
        //无法找到第三方
        $wfzddsf = round($transaction_price * config('car.wfzddsf_rate'), 2);
        //自燃险
        $zrssx = round($transaction_price * config('car.zrssx_rate'), 2);

        //第三者责任险
        foreach ($dszzrx as $item) {
            if ($options['dszzrx'] == $item['id']) {
                $data['dszzrx'] = $item['value'];
                $data['dszzrx_text'] = $item['name'];
            }
        }

        //车辆损失险
        if ($options['clssx']) {
            $data['clssx_option'] = 1;
            $data['clssx'] = $clssx;
        } else {
            $data['clssx_option'] = 0;
            $data['clssx'] = 0;
        }

        //全车盗抢险
        if ($options['qcdqx']) {
            $data['qcdqx_option'] = 1;
            $data['qcdqx'] = $qcdqx;
        } else {
            $data['qcdqx_option'] = 0;
            $data['qcdqx'] = 0;
        }

        //玻璃单独破碎险
        if ($options['blddpsx']) {
            $data['blddpsx_option'] = 1;
            $data['blddpsx'] = $blddpsx;
        } else {
            $data['blddpsx_option'] = 0;
            $data['blddpsx'] = 0;
        }

        //车上人员责任险（司机）
        if ($options['sj_csryzrx']) {
            $data['sj_csryzrx_option'] = 1;
            foreach ($sj_csryzrx as $item) {
                if ($options['sj_csryzrx'] == $item['id']) {
                    $data['sj_csryzrx'] = $item['value'];
                    $data['sj_csryzrx_text'] = $item['name'];
                }
            }
        } else {
            $data['sj_csryzrx_option'] = 0;
            $data['sj_csryzrx'] = 0;
        }

        //车上人员责任险（乘客）
        if ($options['ck_csryzrx']) {
            $data['ck_csryzrx_option'] = 1;
            foreach ($ck_csryzrx as $item) {
                if ($options['ck_csryzrx'] == $item['id']) {
                    $data['ck_csryzrx'] = $item['value'];
                    $data['ck_csryzrx_text'] = $item['name'];
                }
            }
        } else {
            $data['ck_csryzrx_option'] = 0;
            $data['ck_csryzrx'] = 0;
        }

        //无法找到第三方
        if ($options['wfzddsf']) {
            $data['wfzddsf_option'] = 1;
            $data['wfzddsf'] = $wfzddsf;
        } else {
            $data['wfzddsf_option'] = 0;
            $data['wfzddsf'] = 0;
        }

        //自燃险
        if ($options['zrssx']) {
            $data['zrssx_option'] = 1;
            $data['zrssx'] = $zrssx;
        } else {
            $data['zrssx_option'] = 0;
            $data['zrssx'] = 0;
        }

        //判断是否为宝马奔驰（各个保险 * 1.15，除了不计免赔险）
        if (in_array($brand_id, [1, 2])) {
            !empty($data['dszzrx']) ? $data['dszzrx'] = ($data['dszzrx'] * 1.15) : ''; //第三者责任险
            !empty($data['clssx']) ? $data['clssx'] = ($data['clssx'] * 1.15) : ''; //车辆损失险
            !empty($data['qcdqx']) ? $data['qcdqx'] = ($data['qcdqx'] * 1.15) : ''; //全车盗抢险
            !empty($data['blddpsx']) ? $data['blddpsx'] = ($data['blddpsx'] * 1.15) : ''; //玻璃单独破碎险
            !empty($data['sj_csryzrx']) ? $data['sj_csryzrx'] = ($data['sj_csryzrx'] * 1.15) : ''; //车上人员责任险司机
            !empty($data['ck_csryzrx']) ? $data['ck_csryzrx'] = ($data['ck_csryzrx'] * 1.15) : ''; //车上人员责任险乘客
            !empty($data['wfzddsf']) ? $data['wfzddsf'] = ($data['wfzddsf'] * 1.15) : ''; //无法找到第三方
            !empty($data['zrssx']) ? $data['zrssx'] = ($data['zrssx'] * 1.15) : ''; //自燃险
        }

        //不计免赔险
        $data['bjmptyx'] = 0;
        $data['bjmptyx'] += !empty($data['dszzrx']) ? ($data['dszzrx'] * 0.15) : 0; //累加第三者责任险
        $data['bjmptyx'] += !empty($data['clssx']) ? ($data['clssx'] * 0.15) : 0; //累加车辆损失险
        $data['bjmptyx'] += !empty($data['qcdqx']) ? ($data['qcdqx'] * 0.15) : 0; //累加全车盗抢险
        $data['bjmptyx'] += !empty($data['blddpsx']) ? ($data['blddpsx'] * 0.15) : 0; //累加玻璃单独破碎险
        $data['bjmptyx'] += !empty($data['sj_csryzrx']) ? ($data['sj_csryzrx'] * 0.15) : 0; //累加车上人员责任险司机
        $data['bjmptyx'] += !empty($data['ck_csryzrx']) ? ($data['ck_csryzrx'] * 0.15) : 0; //累加车上人员责任险乘客
        $data['bjmptyx'] += !empty($data['wfzddsf']) ? ($data['wfzddsf'] * 0.15) : 0; //累加无法找到第三方
        $data['bjmptyx'] += !empty($data['zrssx']) ? ($data['zrssx'] * 0.15) : 0; //累加自燃险

        //商业险合计
        $data['syxhj'] = $data['dszzrx'] + $data['clssx'] + $data['qcdqx'] + $data['blddpsx'] + $data['sj_csryzrx'] +
                            $data['ck_csryzrx'] + $data['wfzddsf'] + $data['zrssx'] + $data['bjmptyx'];
        //商业险折扣
        $data['syxzk'] = $syxzkbl;
        //折扣后金额
        $data['zkhje'] = $data['syxhj'] * ($syxzkbl / 100);
    }

    function fwf($cjzj, $program, $dkje = 0)
    {
        $cjzj = floor($cjzj);

        if ($cjzj < 100000) {
            $fwf = 2000;
        } elseif (100000 <= $cjzj && $cjzj < 150000) {
            $fwf = 2500;
        } elseif (150000 <= $cjzj && $cjzj < 200000) {
            $fwf = 3000;
        } elseif (200000 <= $cjzj && $cjzj < 250000) {
            $fwf = 3500;
        } elseif (250000 <= $cjzj && $cjzj < 300000) {
            $fwf = 4000;
        } elseif (300000 <= $cjzj && $cjzj < 400000) {
            $fwf = 4500;
        } else {
            $fwf = 5000;
        }

        //非全款购车需要交贷款服务费
        if ($program != 1) {
            if ($dkje < 100000) {
                $fwf += 1000;
            } elseif (100000 <= $dkje && $cjzj < 200000) {
                $fwf += 1500;
            } elseif (200000 <= $dkje && $dkje < 300000) {
                $fwf += 2000;
            } elseif (300000 <= $dkje && $dkje < 400000) {
                $fwf += 2500;
            } elseif (400000 <= $dkje && $dkje < 500000) {
                $fwf += 3000;
            } elseif (500000 <= $dkje && $dkje < 600000) {
                $fwf += 3500;
            } else {
                $fwf += 4000;
            }
        }

        return $fwf;
    }