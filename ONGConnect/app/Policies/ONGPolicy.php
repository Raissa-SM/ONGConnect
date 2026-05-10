<?php

namespace App\Policies;

use App\Models\ONG;
use App\Models\User;

class ONGPolicy
{
    /**
     * Apenas a própria ONG pode atualizar seu perfil.
     */
    public function update(User $user, ONG $ong): bool
    {
        return $user->isOng() && $user->ong?->id === $ong->id;
    }
}
