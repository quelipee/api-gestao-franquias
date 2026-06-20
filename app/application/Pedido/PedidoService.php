<?php

namespace App\application\Pedido;

use App\Contracts\Repository\EstoqueRepositoryContract;
use App\Contracts\Repository\PedidoRepositoryContract;
use App\Contracts\Repository\UnidadeRepositoryContract;
use App\Contracts\Services\PedidoServiceContract;
use App\DTOs\Pedido\PedidoDTO;
use App\Exceptions\EstoqueException;
use App\Exceptions\UnidadeException;
use App\Exceptions\UnidadeProdutoException;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Pedido;
use Illuminate\Support\Facades\DB;
use Throwable;

class PedidoService implements PedidoServiceContract
{
    public function __construct(
        protected UnidadeRepositoryContract $unidadeRepository,
        protected PedidoRepositoryContract  $pedidoRepository,
        protected EstoqueRepositoryContract $estoqueRepository
    )
    {
    }

    /**
     * @throws UnidadeException
     * @throws UnidadeProdutoException
     * @throws EstoqueException
     * @throws Throwable
     */
    public function create(PedidoDTO $pedidoDTO)
    {
        list($subtotal, $desconto, $estoqueMap, $total) = $this->logicaPedido($pedidoDTO);

        return DB::transaction(function () use ($pedidoDTO, $subtotal, $desconto, $total, $estoqueMap) {
            $pedido = $this->pedidoRepository->createPedido($pedidoDTO, [
                'subtotal' => $subtotal,
                'desconto' => $desconto,
                'total' => $total,
            ]);

            foreach ($pedidoDTO->itens as $item) {
                $estoqueItem = $estoqueMap->get($item->produto_id);
                $precoUnitario = $estoqueItem->produto->preco_base;

                $this->pedidoRepository->createItemPedido($pedido, $item, $precoUnitario);
            }
            return $pedido;
        });
    }

    /**
     * @param PedidoDTO $pedidoDTO
     * @return array
     * @throws EstoqueException
     * @throws UnidadeException
     * @throws UnidadeProdutoException
     */
    public function logicaPedido(PedidoDTO $pedidoDTO): array
    {
        $subtotal = 0;
        $desconto = 0;

        $unidade = $this->unidadeRepository->findById($pedidoDTO->unidade_id);

        if (!$unidade) {
            throw UnidadeException::UnidadeInvalida();
        }

        $estoque = $this->estoqueRepository->findByUnidade($unidade);

        $estoqueMap = $estoque->keyBy('produto_id');

        foreach ($pedidoDTO->itens as $item) {
            $estoqueItem = $estoqueMap->get($item->produto_id);

            if (!$estoqueItem) {
                throw UnidadeProdutoException::NaoExisteProduto();
            }
            if ($estoqueItem->quantidade < $item->quantidade) {
                throw EstoqueException::EstoqueInsuficiente();
            }

            $subtotal += $item->quantidade * $estoqueItem->produto->preco_base;
        }

        $total = $subtotal - $desconto;
        return array($subtotal, $desconto, $estoqueMap, $total);
    }
}
