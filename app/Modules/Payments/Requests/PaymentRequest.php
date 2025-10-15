<?php

namespace App\Modules\Payments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'user' => 'sometimes|required|numeric',
            'article' => 'required|numeric',
            'year' => 'sometimes|required|numeric',
            'month' => 'sometimes|required|numeric'
        ];

        return $rules;
    }
}
