<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Worker extends Authenticatable implements JWTSubject
{
    protected $fillable = ['agent_id', 'franchisee_id', 'username', 'password', 'name', 'mobile', 'worker_no',
                            'id_number_image_z', 'id_number_image_f', 'other_image', 'bank_image', 'bank_name',
                            'bank_no', 'bank', 'parent_id', 'user_id', 'qrcode_url', 'client_num', 'client_total_num',
                            'star'];

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

    //获取当前个代级别
    public function getLevelName($level)
    {
        switch ($level) {
            case 1:
                return config('car.level1_name');
            case 2:
                return config('car.level2_name');
            case 3:
                return config('car.level3_name');
            case 4:
                return config('car.level4_name');
        }
    }

    //获取当前管理级别
    public function getManageLevelName($level)
    {
        switch ($level) {
            case 1:
                return config('car.manage_level1_name');
            case 2:
                return config('car.manage_level2_name');
            case 3:
                return config('car.manage_level3_name');
        }
    }

    //获取当前级别星星最大数量
    public function getStarMax($level)
    {
        switch ($level) {
            case 1:
                return config('car.level1_star_max');
            case 2:
                return config('car.level2_star_max');
            case 3:
                return config('car.level3_star_max');
            case 4:
                return config('car.level4_star_max');
        }
    }
}
