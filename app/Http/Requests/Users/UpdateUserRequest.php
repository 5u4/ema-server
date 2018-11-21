<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'username' => 'between:4,14|alpha_num',
        ];
    }

    public function messages()
    {
        return [
            'username.between'   => 'The :attribute field should be between :min and :max characters',
            'username.alpha_num' => 'The :attribute field is alphabet and numerical only',
        ];
    }
}
