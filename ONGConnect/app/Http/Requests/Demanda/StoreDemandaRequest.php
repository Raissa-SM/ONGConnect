<?php

namespace App\Http\Requests\Demanda;

use App\Enums\TipoDemanda;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDemandaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Autorização via Policy no controller
    }

    public function rules(): array
    {
        return [
            'titulo'         => ['required', 'string', 'max:255'],
            'descricao'      => ['required', 'string'],
            'tipo'           => ['required', Rule::in(array_column(TipoDemanda::cases(), 'value'))],
            'data_inicio'    => ['nullable', 'date'],
            'data_limite'    => ['nullable', 'date', 'after_or_equal:data_inicio'],
            'vagas'          => ['nullable', 'integer', 'min:1', 'max:9999'],
            'endereco'       => ['nullable', 'string', 'max:255'],
            'cidade'         => ['nullable', 'string', 'max:100'],
            'uf'             => ['nullable', 'string', 'size:2'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'categorias'     => ['nullable', 'array'],
            'categorias.*'   => ['integer', 'exists:categorias,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'            => 'O título da demanda é obrigatório.',
            'descricao.required'         => 'A descrição é obrigatória.',
            'tipo.required'              => 'O tipo da demanda é obrigatório.',
            'tipo.in'                    => 'Tipo inválido. Use: presencial, doacao ou habilidade.',
            'data_limite.after_or_equal' => 'A data limite deve ser igual ou posterior à data de início.',
            'vagas.min'                  => 'A demanda deve ter ao menos 1 vaga.',
            'categorias.*.exists'        => 'Uma ou mais categorias informadas não existem.',
        ];
    }
}
