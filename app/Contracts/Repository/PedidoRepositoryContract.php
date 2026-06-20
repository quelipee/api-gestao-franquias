<?php

namespace App\Contracts\Repository;

use App\DTOs\Pedido\PedidoDTO;
use App\DTOs\Pedido\PedidoItemDTO;
use App\Models\ItemPedido;
use App\Models\Pedido;

interface PedidoRepositoryContract
{
    public function createPedido(PedidoDTO $data, array $pedido);

    public function createItemPedido(Pedido $pedido, PedidoItemDTO $item, float $precoUnitario): ItemPedido;
}
