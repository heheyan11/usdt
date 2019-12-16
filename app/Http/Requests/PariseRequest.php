<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PariseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['numeric','required'],
            'status' => ['numeric','required'],
        ];
    }

    public function attributes()
    {
        return [
            'id' => '密码',
            'status' => '状态',
        ];
    }

    public function messages()
    {
        return [
            'id.required'=>'id不存在',
            'id.numeric' => 'id必须数字',
            'status.required'=>'status不存在',
            'status.numeric' => 'status必须数字',
        ];
    }
}
