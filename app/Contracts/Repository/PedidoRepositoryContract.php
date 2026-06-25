<?php

namespace App\Contracts\Repository;

use App\DTOs\Pedido\PedidoDTO;
use App\DTOs\Pedido\PedidoItemDTO;
use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Models\Fidelizacao;
use App\Models\ItemPedido;
use App\Models\Pedido;

interface PedidoRepositoryContract
{
    public function createPedido(PedidoDTO $data, array $pedido);

    public function createItemPedido(Pedido $pedido, PedidoItemDTO $item, float $precoUnitario): ItemPedido;

    public function filtro(?CanalPedido $canalPedido, ?OrderStatus $status);

    public function updateStatus(Pedido $pedido, OrderStatus $status): Pedido;

    public function cancelamentoPedido(Pedido $pedido, OrderStatus $status, string $motivo_cancelamento): Pedido;

    public function createFidelizacao(int $user_id): Fidelizacao;
}
