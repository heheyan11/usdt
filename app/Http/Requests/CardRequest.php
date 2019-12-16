<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest
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
            'face' => [
                    'required',

                ],
            'back' => [
                    'required',

                ],
        ];
    }

    public function attributes()
    {
        return [
            'face' => '身份证正面',
            'back' => '身份证反面',
        ];
    }

    public function messages()
    {
        return [
            'face.required'=>'身份证正面不能为空',
            'back.required'=>'身份证反面不能为空',
        ];
    }
}
