<?php

namespace App\Policies;

use App\Models\Demanda;
use App\Models\User;

class DemandaPolicy
{
    public function create(User $user): bool
    {
        return $user->isOng();
    }

    public function update(User $user, Demanda $demanda): bool
    {
        return $user->isOng() && $user->ong?->id === $demanda->ong_id;
    }

    public function delete(User $user, Demanda $demanda): bool
    {
        return $user->isOng() && $user->ong?->id === $demanda->ong_id;
    }
}
