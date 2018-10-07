<?php

namespace App\Http\Controllers\Api;

use App\Models\Worker;
use App\Models\Commission;
use Illuminate\Http\Request;
use App\Transformers\WorkerTransformer;
use App\Transformers\CommissionTransformer;
use EasyWeChat\Factory;

class WorkersController extends Controller
{
    protected $worker;

    public function __construct()
    {
        $this->worker = \Auth::guard('worker_api')->user();
    }

    public function index()
    {
        $workers = Worker::where('parent_id', $this->worker->id)->paginate(10);
        return $this->response->paginator($workers, new WorkerTransformer());
    }

    public function me()
    {
        return $this->response->item($this->worker, new WorkerTransformer());
    }

    public function qrcode()
    {
        $qrcode_url = $this->worker->qrcode_url;

        //判断是否已经存在二维码
        if (!empty($qrcode_url)) {
            return $this->response->array(['qrcode_url' => $qrcode_url]);
        }

        $worker_no = $this->worker->worker_no;

        $miniProgram = \EasyWeChat::miniProgram();

        $response = $miniProgram->app_code->getUnlimit($worker_no, [
            'page' => 'pages/index'
        ]);

        //二维码的存放路径
        $path_qd = "images/qrcode/{$this->worker->id}/";
        $path = "uploads/images/qrcode/{$this->worker->id}/";

        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            $filename = $response->save($path);
        } else {
            return $this->response->errorUnauthorized('系统繁忙');
        }

        //更新业务员二维码
        $qrcode_url = $path_qd . $filename;
        Worker::where('id', $this->worker->id)->update(['qrcode_url' => $qrcode_url]);

        return $this->response->array(['qrcode_url' => $qrcode_url]);
    }

    public function commissionSummary(Request $request)
    {
        $ym = $request->ym;

        $ym_arr = explode('-', $ym);
        $year = $ym_arr[0];
        $month = $ym_arr[1];

        $where = ['worker_id' => $this->worker->id, 'year' => $year, 'month' => $month];
        $summary = \DB::table('commission_month')->where($where)->first();

        return $this->response->array(['commission' => $summary->commission]);
    }

    public function commissions(Request $request)
    {
        $ym = $request->ym;

        $days = date('t', strtotime($ym . '-01'));

        $start_time = $ym . '-01 00:00:00';
        $end_time = $ym . '-' . $days . ' 23:59:59';

        $commissions = Commission::where('worker_id', $this->worker->id)
                                    ->whereBetween('created_at', [$start_time, $end_time])->paginate(10);

        return $this->response->paginator($commissions, new CommissionTransformer());
    }

    public function commission(Request $request)
    {
        $id = $request->id;

        $commission = Commission::find($id);

        return $this->response->item($commission, new CommissionTransformer());
    }

    public function update(Request $request)
    {
        $data = $request->only(['id_number_image_z', 'id_number_image_f', 'other_image', 'bank_name', 'bank', 'bank_no']);

        $data['id_number_image_z'] = config('car.image_domain') . $data['id_number_image_z'];
        $data['id_number_image_f'] = config('car.image_domain') . $data['id_number_image_f'];
        $data['other_image'] = $data['other_image'] ? config('car.image_domain') . $data['other_image'] : '';

        $this->worker->update($data);

        return $this->response->item($this->worker, new WorkerTransformer());
    }
}
