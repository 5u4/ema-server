<?php

namespace App\Http\Requests\Movies;

use Illuminate\Foundation\Http\FormRequest;

class CreateMovieRequest extends FormRequest
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
            'name' => 'required|string|between:1,18',
            'movieId' => 'required|integer|',
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
            'name.between'  => 'The :attribute field should be between :min and :max',
            'movieId.require' => 'The :attribute field is required',
            'movieId.integer' => 'movieId should be a integer',
        ];
    }
}
