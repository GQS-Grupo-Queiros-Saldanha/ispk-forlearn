<?php

namespace App\Modules\Users\Requests;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class GrauAcademicoRequest extends FormRequest
{

/**
 * Get the validation rules that apply to the request.
 *
 * @return array
 */
public function rules()
{
    $id = $this->route('grau_academico'); // Pode ser null na criação

    return [
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grau_academico')->ignore($id),
            ],
            'nome' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grau_academico')->ignore($id),
            ], 
            'abreviacao' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grau_academico')->ignore($id),
            ],
            'descricao' => [
                'required',
                'string',
                'max:255',
                Rule::unique('grau_academico')->ignore($id),
            ], 
    ];
}

}