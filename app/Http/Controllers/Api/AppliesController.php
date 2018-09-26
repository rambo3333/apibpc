<?php

namespace App\Http\Controllers\Api;

use App\Models\Apply;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Requests\Api\ApplyRequest;

class AppliesController extends Controller
{
    public function store(ApplyRequest $request)
    {
        $verifyData = \Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = $this->user();
        $data = [];
        $data = $request->only(['name', 'bank', 'id_number_image_z', 'id_number_image_f', 'other_image', 'bank_image']);

        $apply_info = Apply::where('user_id', $user->id)->first();
        if ($apply_info) {
            return $this->response->errorUnauthorized('请勿重复提交申请');
        }

        //判断手机号是否唯一
        $res = Worker::where('mobile', $verifyData['mobile'])->first();
        if ($res) {
            return $this->response->errorUnauthorized('该手机号已注册');
        }

        //如果存在邀请人，获取该业务员ID，以及该业务员所属加盟商，代理商
        if ($request->worker_no) {
            $worker = Worker::where('worker_no', $request->worker_no)->first();
            $data['worker_id'] = $worker->id;
            $worker->franchisee_id ? $data['franchisee_id'] = $worker->franchisee_id : '';
            $worker->agent_id ? $data['agent_id'] = $worker->agent_id : '';
        }

        $data['id_number_image_z'] = config('car.image_domain') . $data['id_number_image_z'];
        $data['id_number_image_f'] = config('car.image_domain') . $data['id_number_image_f'];
        $data['other_image'] = $data['other_image'] ? config('car.image_domain') . $data['other_image'] : '';
        $data['bank_image'] = config('car.image_domain') . $data['bank_image'];
        $data['mobile'] = $verifyData['mobile'];
        $data['password'] = bcrypt($request->password);
        $data['user_id'] = $user->id;
        $data['status'] = 0;

        Apply::create($data);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return $this->response->created();
    }
}
