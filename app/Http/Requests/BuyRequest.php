<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyRequest extends FormRequest
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

    public function rules()
    {
        return [
            'amount' => [
                'required',
                'regex:/^[0-9]+(.[0-9]{1,4})?$/',
                ],
            'crow_id'=>[
                'required',
            ],
            'password' => ['min:6','alpha_num','required'],
        ];
    }

    public function attributes()
    {
        return [
            'amount' => '申请数量',
            'password' => '支付密码',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => '请输操作数量',
            'amount.regex'=>'申请数量格式不正确',
            'password.min' => '支付密码至少6位',
            'password.alpha_num' => '支付密码格式有误',
            'password.password' => '请输入支付密码',

        ];
    }
}
