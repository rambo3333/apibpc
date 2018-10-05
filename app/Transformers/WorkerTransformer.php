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
            'id_number_image_z' => $worker->id_number_image_z,
            'id_number_image_f' => $worker->id_number_image_f,
            'bank_name' => $worker->bank_name,
            'bank_no' => $worker->bank_no,
            'bank' => $worker->bank,
            'created_at' => $worker->created_at->toDateString()
        ];
    }
}