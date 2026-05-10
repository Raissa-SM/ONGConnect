<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InscricaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'status'        => $this->status->value,
            'status_label'  => $this->status->label(),
            'mensagem'      => $this->mensagem,
            'respondida_em' => $this->respondida_em?->format('d/m/Y H:i'),
            'concluida_em'  => $this->concluida_em?->format('d/m/Y H:i'),
            'demanda'       => $this->whenLoaded('demanda', fn () => [
                'id'     => $this->demanda->id,
                'titulo' => $this->demanda->titulo,
                'ong_id' => $this->demanda->ong_id,
            ]),
            'voluntario'    => $this->whenLoaded('voluntario', fn () => [
                'id'   => $this->voluntario->id,
                'nome' => $this->voluntario->user?->name,
            ]),
            'created_at'    => $this->created_at->format('d/m/Y'),
        ];
    }
}
