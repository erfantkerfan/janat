<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetUserPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_pass' => ['required', 'string', 'min:8'],
            'new_pass' => ['required', 'string', 'min:8', 'confirmed']
        ];
    }
}
