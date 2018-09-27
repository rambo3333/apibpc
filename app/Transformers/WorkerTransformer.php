<?php

namespace App\Transformers;

use App\Models\Worker;
use League\Fractal\TransformerAbstract;

class WorkerTransformer extends TransformerAbstract
{
    public function transform(Worker $worker)
    {
        return [
            'id' => $worker->id,
            'name' => $worker->name,
            'mobile' => $worker->mobile,
            'worker_no' => $worker->worker_no,
            'qrcode_url' => $worker->qrcode_url,
            'created_at' => $worker->created_at->toDateString()
        ];
    }
}