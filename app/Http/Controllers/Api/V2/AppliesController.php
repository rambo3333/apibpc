<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Apply;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V2\ApplyRequest;

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
        $data = $request->only(['name']);

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
            //判断该邀请人是否存在
            if (empty($worker)) {
                return $this->response->errorUnauthorized('请输入正确的介绍人编号');
            }

            $data['worker_id'] = $worker->id;
            $worker->franchisee_id ? $data['franchisee_id'] = $worker->franchisee_id : '';
            $worker->agent_id ? $data['agent_id'] = $worker->agent_id : '';
        }

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
