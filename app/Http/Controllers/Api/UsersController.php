<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Http\Requests\Api\UserRequest;

class UsersController extends Controller
{
    public function me()
    {
        $user = $this->user();

        return $this->response->item($user, new UserTransformer());
    }

    public function weAppUpdate(UserRequest $request)
    {
        $user = $this->user();
        $data = [];

        if ($request->iv && $request->encryptedData) {
            $iv = $request->iv;
            $encryptedData = $request->encryptedData;
            $session = $user->weixin_session_key;

            $miniProgram = \EasyWeChat::miniProgram();
            $decryptedData = $miniProgram->encryptor->decryptData($session, $iv, $encryptedData);

            $data['mobile'] = $decryptedData['phoneNumber'];
        }

        if ($request->nickname) {
            $data['name'] = $request->nickname;
            $data['sex'] = $request->sex;
            $data['avatar'] = $request->avatar;
            $data['province'] = $request->province;
            $data['city'] = $request->city;
        }

        $user->update($data);

        return $this->response->item($user, new UserTransformer());
    }
}
