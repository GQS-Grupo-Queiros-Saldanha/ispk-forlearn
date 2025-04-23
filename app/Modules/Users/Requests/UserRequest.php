<?php

namespace App\Modules\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Users\util\EnumVariable;

class UserRequest extends FormRequest
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
        $domain = EnumVariable::$CONVERT_TO_EMAIL;
        $rules = [
            'name' => 'required|min:1|max:191',
            'full_name' => 'sometimes|required|min:4|max:191',
            'id_number' => ['sometimes','required', 'alpha_num','regex:/^[a-zA-Z0-9]{8}$|^[a-zA-z][0-9]{6}$|^[a-zA-Z0-9]{14}$/'],
            'email' => 'required|email|max:191|regex:/^[A-Za-z0-9._%+-]+'.$domain.'$/|unique:users,email,'.$this->route('user').',id,deleted_at,NULL',
            'password' => 'max:191'
        ];

        // // If creating then password is required
        // if($this->method() === 'POST') {
        //     $rules['password'] .= '|required';
        // }

        return $rules;
    }
}
