<?php

namespace App\Modules\GA\Requests;

use App\Modules\Cms\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
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
            'code' => 'required|max:191|unique:degrees,code,' . $this->route('degree'),
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

        return $rules;
    }
}
