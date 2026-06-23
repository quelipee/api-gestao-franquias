<?php

namespace App\Contracts\Services;

use App\DTOs\Pedido\PedidoDTO;
use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Models\Pedido;
use Illuminate\Http\Request;

interface PedidoServiceContract
{
    public function create(PedidoDTO $pedidoDTO);

    public function listForCanal(?CanalPedido $canalPedido, ?OrderStatus $status);

    public function editPedido(Pedido $pedido, Request $request): Pedido;
}
