<?php

namespace App\Http\Requests\Voluntario;

use Illuminate\Foundation\Http\FormRequest;

class SyncCategoriasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'categorias'   => ['required', 'array'],
            'categorias.*' => ['integer', 'exists:categorias,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'categorias.required'  => 'Informe ao menos uma categoria.',
            'categorias.array'     => 'O campo categorias deve ser um array de IDs.',
            'categorias.*.exists'  => 'Uma ou mais categorias informadas não existem.',
        ];
    }
}
