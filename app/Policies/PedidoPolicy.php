<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Pedido;
use App\Models\User;

class PedidoPolicy
{
    public function update(User $user, Pedido $pedido, OrderStatus $status): bool
    {
        if ($user->role === UserRole::ATENDENTE) {
            $temAcesso = $user->unidades()
                ->where('unidade_id', $pedido->unidade_id)
                ->exists();

            if (!$temAcesso) {
                return false;
            }

            return $status == OrderStatus::Entregue;
        }

        if ($user->role === UserRole::COZINHA) {
            $temAcesso = $user->unidades()
                ->where('unidade_id', $pedido->unidade_id)
                ->exists();

            if (!$temAcesso) {
                return false;
            }

            return in_array($status, [OrderStatus::EmPreparo, OrderStatus::Pronto]);
        }

        if ($user->role === UserRole::CLIENTE) {
            $pedido = $user->pedidos()
                ->where('id', $pedido->id)
                ->exists();

            if (!$pedido) {
                return false;
            }

            return $status === OrderStatus::Cancelado;
        }

        if ($user->role === UserRole::GERENTE) {
            return $user->unidades()
                ->where('unidade_id', $pedido->unidade_id)
                ->exists();
        }

        return false;
    }
}
