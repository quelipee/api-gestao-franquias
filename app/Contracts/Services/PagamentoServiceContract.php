<?php

namespace App\Contracts\Services;

use App\DTOs\Pagamento\ProcessarPagamentoDTO;
use App\Models\Pagamento;
use App\Models\Pedido;

interface PagamentoServiceContract
{
    public function processarPagamento(Pedido $pedido, ProcessarPagamentoDTO $dto): Pagamento;
}
