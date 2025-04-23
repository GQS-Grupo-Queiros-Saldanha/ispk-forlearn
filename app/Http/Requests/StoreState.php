<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreState extends FormRequest
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
        return [
            'initials' => 'required|sometimes|regex:/^[a-zA-Z]*$/u|string|min:1|max:5|unique:states',
            'name' => 'required|sometimes',
            // 'name' => 'required|sometimes|regex:/^[a-zA-Z_ ]*$/u|string|unique:states',
            'states_type' => 'required'
        ];
    }
}
