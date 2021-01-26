<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAllocatedLoan extends FormRequest
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
            'account_id' => ['required', 'integer', 'exists:accounts,id'],
            'loan_id' => ['required', 'integer', 'exists:loans,id'],
            'payroll_deduction' => ['required', 'boolean']
        ];
    }
}
