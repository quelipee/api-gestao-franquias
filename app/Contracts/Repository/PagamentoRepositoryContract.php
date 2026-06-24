<?php

namespace App\Contracts\Repository;

use App\Enums\PagamentoStatus;
use App\Models\Pagamento;
use App\Models\Pedido;

interface PagamentoRepositoryContract
{
    public function createPagamento(Pedido $pedido): Pagamento;

    public function atualizarStatus(Pagamento $pagamento, PagamentoStatus $status, array $data): Pagamento;
}
