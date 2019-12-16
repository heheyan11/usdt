<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'phone' => ['required', 'regex:/^1[3456789]\d{9}$/'],
            'password' => ['min:6','alpha_num'],
            'code' => ['numeric'],
        ];
    }

    public function attributes()
    {
        return [
            'phone' => '手机号',
            'password' => '密码',
            'code' => '验证码',
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '请填写手机号',
            'phone.regex' => '手机号码格式不正确',
            'password.min' => '密码至少6位',
            'password.alpha_num' => '密码必须完全是字母、数字',
            'code.numeric' => '验证码必须是数字'
        ];
    }
}
