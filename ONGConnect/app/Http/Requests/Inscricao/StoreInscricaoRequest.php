<?php

namespace App\Http\Requests\Inscricao;

use Illuminate\Foundation\Http\FormRequest;

class StoreInscricaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mensagem' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'mensagem.max' => 'A mensagem deve ter no máximo 1000 caracteres.',
        ];
    }
}
