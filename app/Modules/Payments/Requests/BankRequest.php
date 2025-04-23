<?php

namespace App\Modules\Payments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BankRequest extends FormRequest
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
            'code' => 'required|max:191|unique:banks,code,' . $this->route('bank'),
            'display_name' => 'required|max:191',
            'account_number' => 'required|max:191|unique:banks,account_number,' . $this->route('bank'),
            'iban' => 'required|max:191|unique:banks,iban,' . $this->route('bank'),
        ];

        return $rules;
    }
}
