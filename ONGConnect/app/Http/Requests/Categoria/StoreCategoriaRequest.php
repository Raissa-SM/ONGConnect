<?php

namespace App\Http\Requests\Categoria;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Qualquer usuário autenticado pode criar categorias
    }

    public function rules(): array
    {
        return [
            'nome'      => ['required', 'string', 'max:255', 'unique:categorias,nome'],
            'descricao' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.unique'   => 'Já existe uma categoria com este nome.',
        ];
    }
}
