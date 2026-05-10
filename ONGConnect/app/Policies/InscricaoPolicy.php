<?php

namespace App\Policies;

use App\Models\Inscricao;
use App\Models\User;

class InscricaoPolicy
{
    // ONG dona da demanda pode aceitar, recusar e concluir
    public function gerenciar(User $user, Inscricao $inscricao): bool
    {
        return $user->isOng() && $user->ong?->id === $inscricao->demanda->ong_id;
    }

    // Voluntário cancela a própria inscrição
    public function cancelar(User $user, Inscricao $inscricao): bool
    {
        return $user->isVoluntario() && $user->voluntario?->id === $inscricao->voluntario_id;
    }
}
