<?php

namespace App\Modules\Payments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
        $rules = [
            'transaction_type' => 'required|in:payment,adjust',
            'transaction_value' => 'required|numeric|min:1',
            'transaction_fulfilled_at' => 'sometimes|required|date|before_or_equal:today',
            'transaction_bank' => 'required_with:transaction_fulfilled_at|numeric',
            'transaction_reference' => 'required_with:transaction_fulfilled_at'
        ];

        return $rules;
    }
}
