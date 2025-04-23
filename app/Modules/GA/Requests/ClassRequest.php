<?php

namespace App\Modules\GA\Requests;

use App\Modules\Cms\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class ClassRequest extends FormRequest
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
            'code' => 'required|max:191|unique:classes,code,' . $this->route('class'),
            'display_name' => 'required|max:191|unique:classes,display_name,' . $this->route('class'),
            'room' => 'required|numeric',
            'vacancies' => 'required|numeric',
            'course' => 'required|numeric',
            'year' => 'required|numeric|min:1|max:5',
        ];

        return $rules;
    }
}
