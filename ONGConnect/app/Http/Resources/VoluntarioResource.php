<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoluntarioResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $usuarioAutenticado = $request->user();
        $eProprioVoluntario = $usuarioAutenticado
            && $usuarioAutenticado->isVoluntario()
            && $usuarioAutenticado->voluntario?->id === $this->id;

        return [
            'id'             => $this->id,
            'nome'           => $this->user->name,
            'telefone'       => $this->telefone,
            'descricao'      => $this->descricao,
            'habilidades'    => $this->habilidades ?? [],
            'disponibilidade' => $this->disponibilidade ?? [],
            'cidade'         => $this->cidade,
            'uf'             => $this->uf,
            'latitude'       => $this->latitude,
            'longitude'      => $this->longitude,
            'categorias'     => CategoriaResource::collection($this->whenLoaded('categorias')),
            'media_avaliacoes' => $this->mediaAvaliacoes(),
            // Dados sensíveis — visíveis apenas para o próprio voluntário
            'email'          => $this->when($eProprioVoluntario, fn () => $this->user->email),
            'cpf'            => $this->when($eProprioVoluntario, fn () => $this->cpf),
            'created_at'     => $this->created_at->format('d/m/Y'),
        ];
    }
}
