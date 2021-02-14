<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
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
            'fund_id' => ['required', 'integer', 'exists:funds,id'],
            'loan_type_id' => ['required', 'integer', 'exists:loan_types,id'],
            'name' => ['required', 'string'],
            'loan_amount' => ['required', 'integer'],
            'number_of_installments' => ['required', 'integer']
        ];
    }
}
