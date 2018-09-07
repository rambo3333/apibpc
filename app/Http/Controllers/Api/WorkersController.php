<?php

namespace App\Http\Controllers\Api;

use App\Models\Worker;
use Illuminate\Http\Request;
use App\Transformers\WorkerTransformer;

class WorkersController extends Controller
{
    public function index()
    {
        $worker = \Auth::guard('worker_api')->user();
        $workers = Worker::where('parent_id', $worker->id)->paginate(10);
        return $this->response->paginator($workers, new WorkerTransformer());
    }

    public function me()
    {
        $worker = \Auth::guard('worker_api')->user();

        return $this->response->item($worker, new WorkerTransformer());
    }
}
