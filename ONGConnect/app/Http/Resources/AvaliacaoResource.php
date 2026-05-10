<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvaliacaoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'nota'       => $this->nota,
            'comentario' => $this->comentario,
            'autor_tipo' => $this->autor_tipo->value,
            // Exibido em avaliações recebidas pelo voluntário (autor = ong)
            'demanda'    => $this->whenLoaded('inscricao', fn () => [
                'id'     => $this->inscricao->demanda_id,
                'titulo' => $this->inscricao->demanda?->titulo,
                'ong'    => $this->inscricao->demanda?->ong?->razao_social,
            ]),
            // Exibido em avaliações recebidas pela ONG (autor = voluntario)
            'voluntario' => $this->whenLoaded('inscricao', fn () => [
                'id'   => $this->inscricao->voluntario_id,
                'nome' => $this->inscricao->voluntario?->user?->name,
            ]),
            'created_at' => $this->created_at->format('d/m/Y'),
        ];
    }
}
