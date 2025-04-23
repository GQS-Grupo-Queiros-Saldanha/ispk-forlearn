<?php

namespace App\Modules\Grades\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
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
            'course_id' => 'required|numeric|exists:courses,id',
            'discipline_id' => 'required|numeric|exists:disciplines,id',
            'student_id' => 'required|numeric|exists:users,id',
            'value' => 'required|numeric'
        ];

        return $rules;
    }
}
