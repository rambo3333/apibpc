<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use Auth;
use Validator;

class AuthorizationsController extends Controller
{
    //普通登录（客户）
    public function store(Request $request)
    {
        $user = User::where('mobile', $request->mobile)->first();
        $token = Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token, 'api')->setStatusCode(201);
    }

    //小程序登录（客户）
    public function weappStore(WeappAuthorizationRequest $request, User $user)
    {
        $code = $request->code;
        $worker_no = $request->worker_no;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 找到 openid 对应的用户
        $user_where = [
            'weapp_openid' => $data['openid']
        ];
        $user_single = User::where($user_where)->first();

        if (!$user_single) {
            $user->weapp_openid = $data['openid'];
            $user->weixin_session_key = $data['session_key'];
            $user->worker_no = $worker_no ?: 0;

            //注册用户
            $user->save();
        } else {
            // 更新用户数据
            $user_single->update(['weixin_session_key' => $data['session_key']]);
            $user = $user_single;
        }

        // 为对应用户创建 JWT
        $token = Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token, 'api')->setStatusCode(201);
    }

    //刷新token（客户）
    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token, 'api');
    }

    //普通登录（业务员）
    public function workerStore(Request $request)
    {
        $credentials['mobile'] = $request->username;
        $credentials['password'] = $request->password;

        if (!$credentials['mobile']) {
            return $this->response->errorUnauthorized('请输入手机号');
        }

        if (!Auth::guard('worker_api')->once($credentials)) {
            return $this->response->errorUnauthorized('用户名或密码错误');
        }

        // 获取对应的用户
        $worker = Auth::guard('worker_api')->getUser();

        // 为对应用户创建 JWT
        $token = Auth::guard('worker_api')->fromUser($worker);

        return $this->respondWithToken($token, 'worker_api')->setStatusCode(201);
    }

    //刷新token（业务员）
    public function workerUpdate()
    {
        $token = Auth::guard('worker_api')->refresh();
        return $this->respondWithToken($token, 'worker_api');
    }

    //删除token（业务员）
    public function destroy()
    {
        Auth::guard('worker_api')->logout();
        return $this->response->noContent();
    }

    protected function respondWithToken($token, $guard)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => \Auth::guard($guard)->factory()->getTTL() * 60,
        ]);
    }
}
