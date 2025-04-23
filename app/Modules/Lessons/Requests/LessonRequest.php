<?php

namespace App\Modules\Lessons\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
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
            'teacher' => 'required|numeric|exists:users,id',
            'occured_at' => 'required|date',
            'discipline' => 'required',
            'regime' => 'required|numeric',
            'summary' => 'required|numeric',
            'attendance' => 'sometimes|required|array'
        ];

        return $rules;
    }
}
