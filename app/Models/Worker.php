<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Worker extends Authenticatable implements JWTSubject
{
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
