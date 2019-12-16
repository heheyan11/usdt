<?php


namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class SetpassRequest extends FormRequest
{
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
            'password' => ['min:6','alpha_num','required'],
        ];
    }

    public function attributes()
    {
        return [
            'password' => '密码',
        ];
    }

    public function messages()
    {
        return [
            'password.required'=>'密码必填',
            'password.min' => '密码至少6位',
            'password.alpha_num' => '密码必须完全是字母、数字',
        ];
    }
}