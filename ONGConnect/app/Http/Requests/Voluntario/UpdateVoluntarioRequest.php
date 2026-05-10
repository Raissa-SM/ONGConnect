<?php

namespace App\Http\Requests\Voluntario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoluntarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autorização via Policy no controller
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'nome'            => ['nullable', 'string', 'max:255'], // nome do usuário
            'cpf'             => ['nullable', 'string', 'size:11', Rule::unique('voluntarios', 'cpf')->ignore($id)],
            'telefone'        => ['nullable', 'string', 'max:20'],
            'descricao'       => ['nullable', 'string'],
            'habilidades'     => ['nullable', 'array'],
            'habilidades.*'   => ['string', 'max:100'],
            'disponibilidade' => ['nullable', 'array'],
            'disponibilidade.*' => ['string', 'max:50'],
            'endereco'        => ['nullable', 'string', 'max:255'],
            'cidade'          => ['nullable', 'string', 'max:100'],
            'uf'              => ['nullable', 'string', 'size:2'],
            'latitude'        => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'       => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.size'      => 'O CPF deve ter exatamente 11 dígitos (sem pontuação).',
            'cpf.unique'    => 'Este CPF já está cadastrado.',
            'uf.size'       => 'Informe a UF com 2 letras (ex: SC).',
        ];
    }
}
