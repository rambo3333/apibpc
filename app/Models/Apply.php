<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apply extends Model
{
    protected $fillable = ['agent_id', 'franchisee_id', 'password', 'name', 'mobile', 'id_number_image_z',
                            'id_number_image_f', 'other_image', 'bank_image', 'bank', 'worker_id', 'user_id', 'status'];
}
