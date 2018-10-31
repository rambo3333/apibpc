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
            'other_image' => $worker->other_image,
            'bank_name' => $worker->bank_name,
            'bank_no' => $worker->bank_no,
            'bank' => $worker->bank,
            'level_name' => $worker->getLevelName($worker->level),
            'star' => $worker->star,
            'client_num' => $worker->client_num,
            'max_client_num' => $worker->getStarMax($worker->level) * config('car.client_to_star'),
            'manage_level_name' => $worker->manage_level ? $worker->getManageLevelName($worker->manage_level) : '',
            'created_at' => $worker->created_at->toDateString()
        ];
    }
}