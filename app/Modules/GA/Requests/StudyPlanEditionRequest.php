<?php

namespace App\Modules\GA\Requests;

use App\Modules\Cms\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class StudyPlanEditionRequest extends FormRequest
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
        $default_language = Language::whereDefault(true)->firstOrFail();

        $rules = [
            'study_plan' => 'sometimes|required|exists:study_plans,id',
            'lective_year' => 'required|exists:lective_years,id',
            // 'year_transition_rule' => 'required|exists:year_transition_rules,id',
            'average_calculation_rule' => 'required|exists:average_calculation_rules,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            // 'block_enrollments' => 'nullable|boolean',
            'max_enrollments' => 'nullable|integer',
        ];

        foreach ($this->request->get('display_name') as $key => $val) {
            $rules['display_name.' . $key] = 'max:191';

            // If it is the default language
            if ($key === $default_language->id) {
                $rules['display_name.' . $key] .= '|required';
            }
        }

        foreach ($this->request->get('abbreviation') as $key => $val) {
            $rules['abbreviation.' . $key] = 'max:191';

            // If it is the default language
            if ($key === $default_language->id) {
                $rules['abbreviation.' . $key] .= '|required';
            }
        }

        foreach ($this->request->get('description') as $key => $val) {
            $rules['description.' . $key] = 'max:191';

            // If it is the default language
            if ($key === $default_language->id) {
                $rules['description.' . $key] .= '|required';
            }
        }

        return $rules;
    }
}
