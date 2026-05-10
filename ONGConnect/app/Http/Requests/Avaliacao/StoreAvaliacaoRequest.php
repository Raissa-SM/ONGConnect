<?php

namespace App\Http\Requests\Avaliacao;

use Illuminate\Foundation\Http\FormRequest;

class StoreAvaliacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autorização via Policy no controller
    }

    public function rules(): array
    {
        return [
            'nota'      => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nota.required' => 'A nota é obrigatória.',
            'nota.between'  => 'A nota deve ser entre 1 e 5.',
            'comentario.max' => 'O comentário deve ter no máximo 1000 caracteres.',
        ];
    }
}
