<?php

namespace App\Http\Requests\Logs;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class DisplayLogRequest
 * @package App\Http\Requests
 * @property int $page
 * @property int $perPage
 */
class ShowLogRequest extends FormRequest
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
            'page' => 'int|min:1',
            'perPage' => 'int|min:1',
        ];
    }
}
