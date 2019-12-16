<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InfoRequest extends FormRequest
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
            'username'=>['required'],
            'sex'=>['required'],
            'headimgurl'=>['required'],
        ];
    }
    public function messages()
    {
        return [
            'username.required'=>'昵称不能为空',
            'sex.required'=>'性别不能为空',
            'headimgurl.required'=>'头像不能为空',
        ];
    }
}
