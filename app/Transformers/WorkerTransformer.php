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
            'username' => $worker->username,
            'worker_no' => $worker->worker_no,
        ];
    }
}