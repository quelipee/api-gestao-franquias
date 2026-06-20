<?php

namespace App\Contracts\Services;

use App\DTOs\Pedido\PedidoDTO;

interface PedidoServiceContract
{
    public function create(PedidoDTO $pedidoDTO);
}
