<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'f_name' => ['required', 'string'],
            'l_name' => ['required', 'string'],
            'father_name' => ['sometimes', 'required', 'string'],
            'SSN' => ['required', 'unique:users,SSN'],
//            'SSN' => ['required', 'melli_code', 'unique:users,SSN'],
            'staff_code' => ['sometimes', 'nullable', 'string', 'unique:users,staff_code'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'salary' => ['sometimes', 'nullable', 'integer'],
            'address' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'nullable'],
            'mobile' => ['sometimes', 'nullable'],
//            'phone' => ['sometimes', 'nullable', 'iran_phone'],
//            'mobile' => ['sometimes', 'required', 'iran_mobile', 'unique:users,mobile'],
            'email' => ['sometimes', 'nullable', 'email', 'unique:users,email'],
            'description' => ['sometimes', 'nullable', 'string'],
            'company_id' => ['sometimes', 'required', 'integer', 'exists:companies,id'],
            'status_id' => ['required', 'integer', 'exists:user_statuses,id'],
            'user_type_id' => ['required', 'integer', 'exists:user_types,id']
        ];
    }
}
