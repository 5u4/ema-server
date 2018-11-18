<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
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
            'amount' => 'required|numeric|digits_between:1, 20',
            'description' => 'required|between:1, 120|nullable',
            'timestamp' => 'integer',
            'tag' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Transaction amount is required',
            'amount.numeric' => 'Transaction amount must be numeric',
            'amount.between' => 'Transaction amount should have between :min and :max digits',

            'description.required' => 'Transaction description is required',
            'description.between' => 'Transaction description should have between :min and :max characters',

            'timestamp.integer' => 'Timestamp should be a timestamp',
        ];
    }
}
