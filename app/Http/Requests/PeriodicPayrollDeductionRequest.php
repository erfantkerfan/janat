<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeriodicPayrollDeductionRequest extends FormRequest
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
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'paid_at' => ['required', 'date_format:Y-m-d H:i:s'],
            'pay_since_date' => ['required', 'date_format:Y-m-d H:i:s'],
            'pay_till_date' => ['required', 'date_format:Y-m-d H:i:s', 'after_or_equal:pay_since_date'],
        ];
    }
}
