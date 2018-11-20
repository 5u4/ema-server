<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateUserPermissionRequest
 * @package App\Http\Requests\Users
 * @property array $permissions
 */
class UpdateUserPermissionRequest extends FormRequest
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
            'permissions'   => 'required|array',
            'permissions.*' => 'required|exists:permissions,id',
        ];
    }
}
