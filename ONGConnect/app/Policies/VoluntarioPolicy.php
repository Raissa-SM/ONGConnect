<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Voluntario;

class VoluntarioPolicy
{
    /**
     * Apenas o próprio voluntário pode atualizar seu perfil.
     */
    public function update(User $user, Voluntario $voluntario): bool
    {
        return $user->isVoluntario() && $user->voluntario?->id === $voluntario->id;
    }
}
