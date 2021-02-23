<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransaction extends FormRequest
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
            'transaction_status_id' => ['required', 'integer', 'exists:transaction_statuses,id'],
            'cost' => ['required', 'integer'],
            'paid_at' => ['required', 'date_format:Y-m-d H:i:s'],
            'manager_comment' => ['sometimes', 'nullable', 'string'],
            'user_comment' => ['sometimes', 'nullable', 'string'],
            'transaction_type' => ['required', 'string'],
        ];
    }
}
