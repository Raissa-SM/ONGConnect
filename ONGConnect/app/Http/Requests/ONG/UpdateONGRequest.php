<?php

namespace App\Http\Requests\ONG;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateONGRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autorização via Policy no controller
    }

    public function prepareForValidation(): void
    {
        $strip = fn(?string $v) => $v !== null ? preg_replace('/\D/', '', $v) : null;

        $this->merge([
            'cnpj'     => $strip($this->cnpj),
            'telefone' => $strip($this->telefone),
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nome'             => ['nullable', 'string', 'max:255'],  // nome do usuário
            'razao_social'     => ['required', 'string', 'max:255'],
            'cnpj'             => ['nullable', 'string', 'size:14', Rule::unique('ongs', 'cnpj')->ignore($id)],
            'telefone'         => ['nullable', 'string', 'max:20'],
            'descricao_missao' => ['nullable', 'string'],
            'endereco'         => ['nullable', 'string', 'max:255'],
            'cidade'           => ['nullable', 'string', 'max:100'],
            'uf'               => ['nullable', 'string', 'size:2'],
            'latitude'         => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'        => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'razao_social.required' => 'A razão social é obrigatória.',
            'cnpj.size'             => 'O CNPJ deve ter exatamente 14 dígitos (sem pontuação).',
            'cnpj.unique'           => 'Este CNPJ já está cadastrado.',
            'uf.size'               => 'Informe a UF com 2 letras (ex: SC).',
        ];
    }
}
