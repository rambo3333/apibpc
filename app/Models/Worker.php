<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Worker extends Authenticatable implements JWTSubject
{
    protected $fillable = ['agent_id', 'franchisee_id', 'username', 'password', 'name', 'mobile', 'worker_no',
                            'id_number_image_z', 'id_number_image_f', 'other_image', 'bank_image', 'bank_name',
                            'bank_no', 'bank', 'parent_id', 'user_id', 'qrcode_url'];

    public function franchisee()
    {
        return $this->belongsTo(Franchisee::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
