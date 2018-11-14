<?php

namespace App\Http\Requests\Avatar;

use Illuminate\Foundation\Http\FormRequest;

class UploadAvatarRequest extends FormRequest
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
        $maxAvatarSize = config('filesystems.max_avatar_size');

        return [
            'avatar' => 'required|image|max:' . $maxAvatarSize,
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
            'avatar.required' => 'The :attribute field is required',
            'avatar.image'    => 'The :attribute field should be a image type',
            'avatar.max'      => 'The :attribute should not be larger than :max bytes',
        ];
    }
}
