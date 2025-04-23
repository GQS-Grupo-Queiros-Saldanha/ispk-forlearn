<?php

namespace App\Modules\Cms\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LanguageRequest extends FormRequest
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
            'code' => 'required|max:191|unique:menus,code,' . $this->route('language'),
            'name' => 'required|max:191',
            'active' => 'required',
        ];

        return $rules;
    }
}
