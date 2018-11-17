<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|between:8,80',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            /* Email */
            'email.required'     => 'The :attribute field is required',
            'email.email'        => 'The :attribute field should be an valid email',
            'email.exists'       => 'The :attribute field is not been registered yet',

            /* Password */
            'password.required'  => 'The :attribute field is required',
            'password.between'   => 'The :attribute field should be between :min and :max'
        ];
    }
}
