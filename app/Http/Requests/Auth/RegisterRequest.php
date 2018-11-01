<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class RegisterRequest
 * @package App\Http\Requests\Auth
 */
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
            'username' => 'required|between:4,14|alpha_num',
            'email'    => 'required|email|unique:users',
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
            /* Username */
            'username.required'  => 'The :attribute field is required',
            'username.between'   => 'The :attribute field should be between :min and :max characters',
            'username.alpha_num' => 'The :attribute field is alphabet and numerical only',

            /* Email */
            'email.required'     => 'The :attribute field is required',
            'email.email'        => 'The :attribute field should be an valid email',
            'unique'             => 'The :attribute field is already been taken',

            /* Password */
            'password.required'  => 'The :attribute field is required',
            'password.between'   => 'The :attribute field should be between :min and :max'
        ];
    }
}
