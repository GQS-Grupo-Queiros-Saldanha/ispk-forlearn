<?php

namespace App\Modules\Users\Requests;

use App\Modules\Cms\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class MatriculationRequest extends FormRequest
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
            'years' => 'required|array|min:1',
            'years.*' => 'required|numeric|distinct|min:1|max:6',
            'classes' => 'required|array|min:1',
            'classes.*' => 'nullable|numeric|distinct',
            'disciplines' => 'required|array|min:1',
            'disciplines_exam_only' => 'sometimes|required|array'
        ];

        return $rules;
    }
}
