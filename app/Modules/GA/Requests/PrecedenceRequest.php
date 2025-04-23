<?php

namespace App\Modules\GA\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrecedenceRequest extends FormRequest
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
            'study_plan_edition' => 'required|exists:study_plan_editions,id',
            'discipline' => 'nullable|exists:disciplines,id',
            'precedence' => 'nullable|exists:disciplines,id',
        ];

        return $rules;
    }
}
