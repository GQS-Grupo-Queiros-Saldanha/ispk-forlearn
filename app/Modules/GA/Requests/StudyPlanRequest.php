<?php

namespace App\Modules\GA\Requests;

use App\Modules\Cms\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class StudyPlanRequest extends FormRequest
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
            'code' => 'required|max:191|unique:study_plans,code,' . $this->route('study_plan'),
        ];

        foreach($this->request->get('display_name') as $key => $val)
        {
            $rules['display_name.'.$key] = 'max:191';

            // If it is the default language
            if ($key === $default_language->id) {
                $rules['display_name.' . $key] .= '|required';
            }
        }

        foreach($this->request->get('description') as $key => $val)
        {
            $rules['description.'.$key] = 'max:191';

            // If it is the default language
            if ($key === $default_language->id) {
                $rules['description.' . $key] .= '|required';
            }
        }

        foreach($this->request->get('abbreviation') as $key => $val)
        {
            $rules['abbreviation.'.$key] = 'max:191';

            // If it is the default language
            if ($key === $default_language->id) {
                $rules['abbreviation.' . $key] .= '|required';
            }
        }

        return $rules;
    }
}
