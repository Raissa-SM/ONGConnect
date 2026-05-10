<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ONGResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'razao_social'     => $this->razao_social,
            'cnpj'             => $this->cnpj,
            'telefone'         => $this->telefone,
            'descricao_missao' => $this->descricao_missao,
            'endereco'         => $this->endereco,
            'cidade'           => $this->cidade,
            'uf'               => $this->uf,
            'latitude'         => $this->latitude,
            'longitude'        => $this->longitude,
            'usuario'          => [
                'id'   => $this->user->id,
                'nome' => $this->user->name,
            ],
            'total_demandas' => $this->whenLoaded('demandas', fn() => $this->demandas->count()),
            'created_at'     => $this->created_at->format('d/m/Y'),
        ];
    }
}
