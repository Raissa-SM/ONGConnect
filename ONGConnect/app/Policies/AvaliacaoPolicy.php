<?php

namespace App\Policies;

use App\Models\Inscricao;
use App\Models\User;

class AvaliacaoPolicy
{
    /**
     * ONG avalia o voluntário; voluntário avalia a ONG.
     * A inscrição já deve estar concluída.
     * Cada lado só pode avaliar uma vez (unique constraint na tabela).
     */
    public function create(User $user, Inscricao $inscricao): bool
    {
        if (!$inscricao->podeAvaliar()) {
            return false;
        }

        if ($user->isOng()) {
            return $user->ong?->id === $inscricao->demanda->ong_id;
        }

        if ($user->isVoluntario()) {
            return $user->voluntario?->id === $inscricao->voluntario_id;
        }

        return false;
    }
}
