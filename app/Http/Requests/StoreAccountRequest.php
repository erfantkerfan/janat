<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
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
            'joined_at' => ['required', 'date'],
            'monthly_payment' => ['required', 'integer'],
            'payroll_deduction' => ['sometimes', 'nullable', 'boolean'],
            'fund_id' => ['required', 'integer', 'exists:funds,id'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'user_id' => ['required', 'integer', 'exists:users,id']
        ];
    }
}
