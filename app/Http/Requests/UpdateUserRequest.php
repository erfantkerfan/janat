<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'SSN' => ['required', "unique:users,SSN,{$this->user->id}"], // , 'melli_code'
            'staff_code' => ['sometimes', 'nullable', 'string', "unique:users,staff_code,{$this->user->id}"],
            'salary' => ['sometimes', 'nullable', 'integer'],
            'address' => ['sometimes', 'nullable', 'string'],
            'phone' => ['sometimes', 'nullable', 'integer'],
            'mobile' => ['sometimes', 'nullable'], // , 'iran_mobile'
            'email' => ['sometimes', 'nullable', 'email'],
            'description' => ['sometimes', 'nullable', 'string'],
            'company_id' => ['sometimes', 'required', 'integer', 'exists:companies,id'],
            'status_id' => ['required', 'integer', 'exists:user_statuses,id'],
            'user_type_id' => ['required', 'integer', 'exists:user_types,id']
        ];
    }
}
