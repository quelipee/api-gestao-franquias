<?php

namespace App\Infrastructure\Repository\Pagamento;

use App\Contracts\Repository\PagamentoRepositoryContract;
use App\Enums\PagamentoStatus;
use App\Enums\TipoPagamento;
use App\Models\Pagamento;
use App\Models\Pedido;

class PagamentoRepository implements PagamentoRepositoryContract
{
    public function createPagamento(Pedido $pedido): Pagamento
    {
        return Pagamento::create([
            'pedido_id' => $pedido->id,
            'forma_pagamento' => TipoPagamento::MOCK->value,
            'valor' => $pedido->total,
        ]);
    }

    public function atualizarStatus(Pagamento $pagamento, PagamentoStatus $status, array $data): Pagamento
    {
        $pagamento->update([
            'status' => $status,
            ...$data
        ]);
        return $pagamento->fresh();
    }
}
