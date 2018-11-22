<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class CreateReviewRequest extends FormRequest
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
            'reviewTitle' => 'required|string',
            'reviewContent' => 'required|string',
            'movieId' => 'required|integer',
            'rate' => 'required|integer|between:1,5'
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
            'reviewContent.required' => 'The :attribute field is required',
            'reviewTitle.required' => 'The :attribute field is required',
            'movieId.required' => 'The :attribute field is required',
            'movieId.integer' => 'The :attribute field must be integer',
            'rate.required' => 'The :attribute field is required',
            'rate.integer' => 'The :attribute field must be integer',
            'rate.between'  => 'The :attribute field should be between :min and :max',
        ];
    }
}
