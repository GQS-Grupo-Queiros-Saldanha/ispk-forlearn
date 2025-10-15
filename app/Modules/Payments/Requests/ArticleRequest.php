<?php

namespace App\Modules\Payments\Requests;

use App\Modules\Cms\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
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
            // 'code' => 'required|max:191|unique:articles,code,' . $this->route('article'),
            'code' => 'required|max:191',
            'base_value' => 'required|numeric|min:0|max:500000',
        ];

        foreach ($this->request->get('display_name') as $key => $val) {
            $rules['display_name.' . $key] = 'max:191';

            // If it is the default language
            if ($key === $default_language->id) {
                $rules['display_name.' . $key] .= '|required';
            }
        }

        foreach ($this->request->get('description') as $key => $val) {
            $rules['description.' . $key] = 'max:191';

            // If it is the default language
            // if ($key === $default_language->id) {
            //     $rules['description.' . $key] .= '|required';
            // }
        }

        // Extra Fees
        if ($this->request->get('extra_fees_percent')) {
            foreach ($this->request->get('extra_fees_percent') as $key => $val) {
                $rules['extra_fees_percent.' . $key] = "required_with:extra_fees_delay.$key|numeric|min:0|max:100";
            }
        }

        if ($this->request->get('extra_fees_delay')) {
            foreach ($this->request->get('extra_fees_delay') as $key => $val) {
                $rules['extra_fees_delay.' . $key] = "required_with:extra_fees_percent.$key|numeric|min:1|max:100";
            }
        }

        // Monthly Charges
        if ($this->request->get('monthly_charge_course')) {
            foreach ($this->request->get('monthly_charge_course') as $key => $val) {
                $rules['monthly_charge_course.' . $key] =
                    "required_with:monthly_charge_course_year.$key,monthly_charge_start_month.$key,monthly_charge_end_month.$key,monthly_charge_charge_day.$key|numeric";
            }
        }

        if ($this->request->get('monthly_charge_course_year')) {
            foreach ($this->request->get('monthly_charge_course_year') as $key => $val) {
                $rules['monthly_charge_course_year.' . $key] =
                    "required_with:monthly_charge_course.$key,monthly_charge_start_month.$key,monthly_charge_end_month.$key,monthly_charge_charge_day.$key|numeric|min:1|max:5";
            }
        }

       if ($this->request->get('monthly_charge_start_month')) {
            foreach ($this->request->get('monthly_charge_start_month') as $key => $val) {
                $rules['monthly_charge_start_month.' . $key] =
                    // "required_with:monthly_charge_course.$key,monthly_charge_course_year.$key,monthly_charge_end_month.$key,monthly_charge_charge_day.$key|numeric|lt:monthly_charge_end_month.$key";
                    "required_with:monthly_charge_course.$key,monthly_charge_course_year.$key,monthly_charge_end_month.$key,monthly_charge_charge_day.$key|numeric";
            }
        }

        if ($this->request->get('monthly_charge_end_month')) {
            foreach ($this->request->get('monthly_charge_end_month') as $key => $val) {
                $rules['monthly_charge_end_month.' . $key] =
                    // "required_with:monthly_charge_course.$key,monthly_charge_course_year.$key,monthly_charge_start_month.$key,monthly_charge_charge_day.$key|numeric|gt:monthly_charge_start_month.$key";
                    "required_with:monthly_charge_course.$key,monthly_charge_course_year.$key,monthly_charge_start_month.$key,monthly_charge_charge_day.$key|numeric";
            }
        }

        if ($this->request->get('monthly_charge_charge_day')) {
            foreach ($this->request->get('monthly_charge_charge_day') as $key => $val) {
                $rules['monthly_charge_charge_day.' . $key] =
                    "required_with:monthly_charge_course.$key,monthly_charge_course_year.$key,monthly_charge_start_month.$key,monthly_charge_end_month.$key|numeric|min:1|max:31";
            }
        }

        return $rules;
    }
}
