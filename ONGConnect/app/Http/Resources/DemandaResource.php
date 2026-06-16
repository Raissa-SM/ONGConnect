<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DemandaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'titulo'            => $this->titulo,
            'descricao'         => $this->descricao,
            'tipo'              => $this->tipo->value,
            'tipo_label'        => $this->tipo->label(),
            'status'            => $this->status->value,
            'status_label'      => $this->status->label(),
            'data_inicio'       => $this->data_inicio?->format('d/m/Y'),
            'data_limite'       => $this->data_limite?->format('d/m/Y'),
            'evento_inicio'     => $this->evento_inicio?->format('d/m/Y H:i'),
            'evento_fim'        => $this->evento_fim?->format('d/m/Y H:i'),
            'evento_label'      => $this->evento_label,
            'evento_em_andamento' => $this->eventoEmAndamento(),
            'vagas'             => $this->vagas,
            'vagas_disponiveis' => $this->vagasDisponiveis(),
            'endereco'          => $this->endereco,
            'cidade'            => $this->cidade,
            'uf'                => $this->uf,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'ong'               => $this->whenLoaded('ong', fn () => [
                'id'           => $this->ong->id,
                'razao_social' => $this->ong->razao_social,
                'cidade'       => $this->ong->cidade,
            ]),
            'categorias'        => CategoriaResource::collection($this->whenLoaded('categorias')),
            'total_inscricoes'  => $this->whenLoaded('inscricoes', fn () => $this->inscricoes->count()),
            'created_at'        => $this->created_at->format('d/m/Y'),
        ];
    }
}
