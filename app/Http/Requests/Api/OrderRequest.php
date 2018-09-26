<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'program_id' => 'required',
            'cmodel_id' => 'required',
            'dszzrx' => 'required',
            'clssx' => 'required',
            'qcdqx' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('program_id') != 1 && empty($this->input('qcdqx'))) {
                        $fail('该方案必须购买全车盗抢险');
                        return;
                    }
                },
            ],
            'wfzddsf' => 'required',
            'dkfs' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('program_id') != 1 && empty($this->input('dkfs'))) {
                        $fail('该方案必须选择贷款方式');
                        return;
                    }
                },
            ],
            'ygqs' => [
                function ($attribute, $value, $fail) {
                    if ($this->input('program_id') != 1 && empty($this->input('ygqs'))) {
                        $fail('该方案必须选择期数');
                        return;
                    }
                },
            ],
        ];
    }
}
