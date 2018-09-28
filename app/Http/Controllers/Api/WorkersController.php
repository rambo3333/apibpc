<?php

namespace App\Http\Controllers\Api;

use App\Models\Worker;
use Illuminate\Http\Request;
use App\Transformers\WorkerTransformer;
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
}
