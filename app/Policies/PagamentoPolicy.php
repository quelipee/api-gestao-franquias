<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Enums\PagamentoStatus;
use App\Models\Pedido;
use App\Models\User;

class PagamentoPolicy
{
    public function create(User $user, Pedido $pedido)
    {
        if ($user->id !== $pedido->user_id) {
            return false;
        }

        if (!$this->pedidoPodeReceberPagamento($pedido)) {
            return false;
        }

        return !$this->pedidoJaFoiPago($pedido);
    }

    private function pedidoPodeReceberPagamento(Pedido $pedido): bool
    {
        return $pedido->status === OrderStatus::AguardandoPagamento;
    }

    private function pedidoJaFoiPago(Pedido $pedido): bool
    {
        return $pedido->pagamentos()
            ->where('status', PagamentoStatus::Aprovado->value)
            ->exists();

    }
}
