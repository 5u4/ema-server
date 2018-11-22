<?php

namespace App\Http\Requests\Dining;

use Illuminate\Foundation\Http\FormRequest;

class CreateDiningRequest extends FormRequest
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
            'name' => 'required|string',
            'rest_id' => 'required|string',
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
            'name.required' => 'The :attribute field is required',
            'rest_id.require' => 'The :attribute field is required',
            'rest_id.string' => 'movieId should be a string',
        ];
    }
}
