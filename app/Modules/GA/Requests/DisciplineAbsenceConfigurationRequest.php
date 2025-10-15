<?php

namespace App\Modules\GA\Requests;

use App\Modules\Cms\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class DisciplineAbsenceConfigurationRequest extends FormRequest
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

        ];



        return $rules;
    }
}
