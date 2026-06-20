<?php

namespace App\Infrastructure\Repository\Pedido;

use App\Contracts\Repository\PedidoRepositoryContract;
use App\DTOs\Pedido\PedidoDTO;
use App\Models\ItemPedido;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Throwable;

class PedidoRepository implements PedidoRepositoryContract
{
    /**
     * @throws Throwable
     */
    public function createPedido(PedidoDTO $data, array $pedido)
    {
        return Pedido::create([
            'unidade_id' => $data->unidade_id,
            'user_id' => auth()->id(),
            'canal_pedido' => $data->canal_pedido,
            'subtotal' => $pedido['subtotal'],
            'desconto' => $pedido['desconto'],
            'total' => $pedido['total'],
        ]);
    }

    public function createItemPedido($pedido, $item, $precoUnitario): ItemPedido
    {
        return ItemPedido::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $item->produto_id,
            'quantidade' => $item->quantidade,
            'preco_unitario' => $precoUnitario,
            'subtotal' => $item->quantidade * $precoUnitario,
        ]);
    }
}
