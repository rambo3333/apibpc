<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class ApplyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'bail|required|unique:applies|between:3,20|regex:/^[A-Za-z0-9\-\_]+$/|',
            'password' => 'required|confirmed|string|min:6',
            'name' => 'required',
            'id_number_image_z' => 'required',
            'id_number_image_f' => 'required',
            'bank_image' => 'required',
            'bank' => 'required',
            'verification_key' => 'required|string',
            'verification_code' => 'required|string',
        ];
    }

    public function attributes()
    {
        return [
            'verification_key' => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }
}
