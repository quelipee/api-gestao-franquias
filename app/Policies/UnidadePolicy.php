<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Unidade;
use App\Models\User;

class UnidadePolicy
{
    /**
     * Create a new policy instance.
     */
    public function gerenciar(User $user, Unidade $unidade): bool
    {
        if ($user->role === UserRole::ADMIN) {
            return true;
        }

        if ($user->role === UserRole::GERENTE) {
            return $user->unidades()
                ->where('unidade_id', $unidade->id)
                ->exists();
        }

        return false;
    }
}
