<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IdcRequest extends FormRequest
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
            'name' => [
                'required',
                'regex:/^([\xe4-\xe9][\x80-\xbf]{2}){2,4}$/'
            ],
            'code' => [
                'required',
                'between:15,18',
            ],

        ];
    }
    public function messages()
    {
        return [
            'name.required'=>'姓名不能为空',
            'name.regex'=>'姓名必须是中文',
            'code.required'=>'身份证号不能为空',
            'code.between'=>'身份证号必须是15或18位',
        ];
    }
}
