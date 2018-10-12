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
        $program_id = $request->program_id; //购车方案（1：全款购车 2：贷款购车 3：低首付零首付）
        $dkfs = $request->dkfs; //贷款方式（1：银行贷款、2：担保贷款、3：金融贷款、4：厂家贷款）
        $sfk = $request->sfk; //首付款选项（贷款购车：1：30%、2：40%、3：50%、4：自定义）（低首付购车：1：0%、2：5%、3：10%、4：20% 5：自定义）
        $sfk_value = $request->sfk_value; //首付款自定义的金额
        $qs = $request->ygqs; //月供期数（贷款购车：1:12期、2:24期、3:36期、4:48期、5:60期）（低首付购车：1:36期、2:48期、3:60期）

        $program_text_arr = [
            1 => '全款',
            2 => '贷款',
            3 => '低首付零首付'
        ];

        $dkfs_text_arr = [
            1 => '银行贷款',
            2 => '担保贷款',
            3 => '金融贷款',
            4 => '厂家金融'
        ];

        $dk_sfk_options = [
            1 => 30,
            2 => 40,
            3 => 50,
            4 => '自定义',
        ];

        $dsf_sfk_options = [
            1 => 0,
            2 => 5,
            3 => 10,
            4 => 20,
            5 => '自定义',
        ];

        $gps_text_arr = [
            1 => 0,
            2 => 500,
            3 => 500,
            4 => '咨询客服'
        ];

        $dk_qs_text_arr = [
            1 => '12期',
            2 => '24期',
            3 => '36期',
            4 => '48期',
            5 => '60期',
        ];

        $dsf_qs_text_arr = [
            1 => '36期',
            2 => '48期',
            3 => '60期',
        ];

        //贷款购车：各个贷款方式的万元系数
        $dk_dkfs_xs_arr = [
            1 => 305,
            2 => 319,
            3 => 327,
        ];

        $dsf_xs_arr = [
            1 => 327,
            2 => 260,
            3 => 221
        ];

        //获取车型数据
        $cmodel = Cmodel::find($cmodel_id);
        //成交价
        $price = $cmodel->guide_price - $cmodel->pre_amount;

        //商业保险选项
        $options['dszzrx'] = $request->dszzrx; //第三者责任险（1：50万、2：100万、3：150万）
        $options['clssx'] = $request->clssx; //车辆损失险（0：未选、1：已选）
        $options['qcdqx'] = $request->qcdqx; //全车盗抢险（0：未选、1：已选）
        $options['blddpsx'] = $request->blddpsx; //玻璃单独破碎险（0：未选、1：已选）
        $options['sj_csryzrx'] = $request->sj_csryzrx; //车上人员责任险，司机（0：未选、1：1万/座、2：2万/座、3：5万/座）
        $options['ck_csryzrx'] = $request->ck_csryzrx; //车上人员责任险，乘客（0：未选、1：1万/座、2：2万/座、3：5万/座）
        $options['wfzddsf'] = $request->wfzddsf; //无法找到第三方（0：未选、1：已选）
        $options['zrssx'] = $request->zrssx; //自燃损失险（0：未选、1：已选）


        //获取用户数据
        $user = \Auth::guard('api')->user();

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
        //品牌名称
        $data['brand'] = $cmodel->brand['name'];
        //车系名称
        $data['series'] = $cmodel->series['name'];
        //车型名称
        $data['cmodel'] = $cmodel->name;
        //车型图片
        $data['image'] = $cmodel->image;
        //车型ID
        $data['cmodel_id'] = $request->cmodel_id;
        //指导价
        $data['guide_price'] = $cmodel->guide_price;
        //优惠额度
        $data['pre_amount'] = $cmodel->pre_amount;
        //成交价
        $data['price'] = $price;
        //商业保险
        syx($data, $price, $cmodel->brand_id, $options, $cmodel->syxzkbl);
        //成交总价
        $data['cjzj'] = $data['price'] + $data['gzs'] + $data['spf'] + $data['ccs'] + $data['jqx'] + $data['zkhje'];
        //全款车落地服务费
        $data['fwf'] = fwf($data['cjzj'], $program_id);
        //支付状态
        $data['status'] = 'not_paid';

        //贷款购车
        if ($program_id == 2) {
            //贷款方式
            $data['dkfs'] = $dkfs_text_arr[$dkfs];

            //首付款文本及首付款金额
            if ($sfk == 4) {
                $data['sfk_text'] = $dk_sfk_options[$sfk];
                $data['sfk'] = $sfk_value;
            } else {
                $data['sfk_text'] = $dk_sfk_options[$sfk] . '%';
                $data['sfk'] = $price * ($dk_sfk_options[$sfk] / 100);
            }

            //贷款金额
            $dkje_arr = explode(',', number_format($price - $data['sfk']));
            $data['dkje'] = $dkje_arr[0] * 1000;

            //贷款金额要千位取整，其余部分挪到首付款
            $data['sfk'] += $dkje_arr[1];

            //判断首付金额是否超过7成
            if ($data['sfk'] > intval($price * 0.7)) {
                return $this->response->error('首付金额不能超过70%', 422);
            }

            //抵押金
            $data['dyf'] = config('car.dyf');
            //GPS
            $data['gps'] = $gps_text_arr[$dkfs];
            //手续费等杂费
            $data['sxf'] = 0;
            //续保金
            $data['xbj'] = 0;
            //首付款总额（首付款+购置税+上牌费+车船税+交强险+商业险+抵押费+GPS+手续费等杂费+续保金）
            $data['sfk_total'] = $data['sfk'] + $data['gzs'] + $data['spf'] + $data['ccs'] + $data['jqx'] +
                                    $data['zkhje'] + $data['dyf'] + intval($data['gps']) + $data['sxf'] + $data['xbj'];
            //月供金额（贷款金额 * 万元系数 / 10000 = 月供）
            $data['ygje'] = ($dkfs == 4) ? '咨询客服' : round(($dk_dkfs_xs_arr[$dkfs] * $data['dkje'] / 10000), 2);
            //月供期数
            $data['ygqs'] = $dk_qs_text_arr[$qs];
            //加上贷款金服务费
            $data['fwf'] = fwf($data['cjzj'], $program_id, $data['dkje']);
        }

        //低首付零首付
        if ($program_id == 3) {
            //贷款方式
            $data['dkfs'] = $dkfs_text_arr[$dkfs];

            //首付款文本及首付款金额
            if ($sfk == 5) {
                $data['sfk_text'] = $dsf_sfk_options[$sfk];
                $data['sfk'] = $sfk_value;
            } else {
                $data['sfk_text'] = $dsf_sfk_options[$sfk] . '%';
                $data['sfk'] = $price * ($dsf_sfk_options[$sfk] / 100);
            }

            //贷款金额
            $dkje_arr = explode(',', number_format($price - $data['sfk']));
            $data['dkje'] = $dkje_arr[0] * 1000;

            //贷款金额要千位取整，其余部分挪到首付款
            $data['sfk'] += $dkje_arr[1];

            //判断首付金额是否超过7成
            if ($data['sfk'] > intval($price * 0.7)) {
                return $this->response->error('首付金额不能超过70%', 422);
            }

            //抵押金
            $data['dyf'] = config('car.dyf');
            //GPS
            $data['gps'] = 1680;
            //手续费等杂费
            $data['sxf'] = 0;
            //续保金
            $data['xbj'] = 0;
            //首付款总额（首付款+购置税+上牌费+车船税+交强险+商业险+抵押费+GPS+手续费等杂费+续保金）
            $data['sfk_total'] = $data['sfk'] + $data['gzs'] + $data['spf'] + $data['ccs'] + $data['jqx'] +
                                    $data['zkhje'] + $data['dyf'] + intval($data['gps']) + $data['sxf'] + $data['xbj'];
            //月供金额（贷款金额 * 万元系数 / 10000 = 月供）
            $data['ygje'] = round(($dsf_xs_arr[$qs] * $data['dkje'] / 10000), 2);
            //月供期数
            $data['ygqs'] = $dsf_qs_text_arr[$qs];
            //加上贷款金服务费
            $data['fwf'] = fwf($data['cjzj'], $program_id, $data['dkje']);
        }

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
