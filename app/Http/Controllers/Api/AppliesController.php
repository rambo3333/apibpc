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
        $data = $request->only(['username', 'name', 'bank', 'id_number_image_z', 'id_number_image_f', 'other_image', 'bank_image']);

        $apply_info = Apply::where('user_id', $user->id)->first();
        if ($apply_info) {
            return $this->response->errorUnauthorized('请勿重复提交申请');
        }

        //如果存在邀请人，获取该业务员ID，以及该业务员所属加盟商，代理商
        if ($request->worker_no) {
            $worker = Worker::where('worker_no', $this->worker_no)->with('franchisee', 'agent')->first();
            $data['worker_id'] = $worker->id;
            $worker->franchisee->id ? $data['franchisee_id'] = $worker->franchisee->id : '';
            $worker->agent->id ? $data['agent_id'] = $worker->agent->id : '';
        }

        $data['mobile'] = $verifyData['mobile'];
        $data['password'] = bcrypt($request->password);
        $data['user_id'] = $user->id;

        Apply::create($data);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return $this->response->created();
    }
}
