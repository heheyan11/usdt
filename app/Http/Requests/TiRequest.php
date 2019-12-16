<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => [
                'required',
                'regex:/^[0-9]+(.[0-9]{1,4})?$/',

            ],
            'password' => ['min:6','alpha_num','required'],
            'address'=>[
                'required',
                function($attr,$value,$fail){
                    if (!(preg_match('/^(1|3)[a-zA-Z\d]{24,33}$/', $value) && preg_match('/^[^0OlI]{25,34}$/', $value))) {
                        $fail('地址不合法');
                    }
                }
            ]
        ];
    }
    public function messages()
    {
        return [
            'amount.required' => '请输操作数量',
            'amount.regex'=>'提币数量格式不正确',
            'password.min' => '支付密码至少6位',
            'password.alpha_num' => '支付密码格式有误',
            'password.password' => '请输入支付密码',
            'address'=>'',
        ];
    }
}
