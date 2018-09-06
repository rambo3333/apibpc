<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Transformers\WorkerTransformer;

class WorkersController extends Controller
{
    public function me()
    {
        $worker = \Auth::guard('worker_api')->user();

        return $this->response->item($worker, new WorkerTransformer());
    }
}
