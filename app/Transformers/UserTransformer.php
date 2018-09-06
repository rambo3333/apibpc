<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'mobile' => $user->mobile,
            'sex' => $user->sex,
            'avatar' => $user->avatar,
            'province' => $user->province,
            'city' => $user->city
        ];
    }
}