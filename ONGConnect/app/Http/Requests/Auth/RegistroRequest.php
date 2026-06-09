<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $strip = fn(?string $v) => $v !== null ? preg_replace('/\D/', '', $v) : null;

        $this->merge([
            'cpf'      => $strip($this->cpf),
            'cnpj'     => $strip($this->cnpj),
            'telefone' => $strip($this->telefone),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'unique:users,email'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
            'tipo_perfil'      => ['required', 'in:ong,voluntario'],
            'razao_social'     => ['required_if:tipo_perfil,ong', 'nullable', 'string', 'max:255'],
            'cnpj'             => ['nullable', 'string', 'size:14'],
            'descricao_missao' => ['nullable', 'string'],
            'cpf'              => ['nullable', 'string', 'size:11'],
            'descricao'        => ['nullable', 'string'],
            'telefone'         => ['nullable', 'string', 'max:20'],
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
            'name.required'            => 'O nome é obrigatório.',
            'email.required'           => 'O e-mail é obrigatório.',
            'email.unique'             => 'Este e-mail já está em uso.',
            'password.required'        => 'A senha é obrigatória.',
            'password.min'             => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed'       => 'A confirmação de senha não confere.',
            'tipo_perfil.required'     => 'Informe o tipo de perfil (ong ou voluntario).',
            'tipo_perfil.in'           => 'O tipo de perfil deve ser "ong" ou "voluntario".',
            'razao_social.required_if' => 'A razão social é obrigatória para ONGs.',
        ];
    }
}
