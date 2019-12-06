<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhoneRequest extends FormRequest
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
            'phone'=>['required','regex:/^1[3456789]\d{9}$/','unique:users'],
        ];
    }
    public function attributes()
    {
        return [
            'phone' => '手机号'
        ];
    }

    public function messages()
    {
        return [
            'phone.required' => '请填写手机号',
            'phone.regex' => '手机号码格式不正确',
            'phone.unique' => '该手机号已被注册',
        ];
    }
}
