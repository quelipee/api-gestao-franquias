<?php

namespace App\application\Pedido;

use App\Contracts\Repository\EstoqueRepositoryContract;
use App\Contracts\Repository\PagamentoRepositoryContract;
use App\Contracts\Repository\PedidoRepositoryContract;
use App\Contracts\Repository\UnidadeRepositoryContract;
use App\Contracts\Services\PedidoServiceContract;
use App\DTOs\Pedido\PedidoDTO;
use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Exceptions\EstoqueException;
use App\Exceptions\InvalidOrderStatusTransitionException;
use App\Exceptions\UnidadeException;
use App\Exceptions\UnidadeProdutoException;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PedidoService implements PedidoServiceContract
{
    public function __construct(
        protected UnidadeRepositoryContract   $unidadeRepository,
        protected PedidoRepositoryContract    $pedidoRepository,
        protected EstoqueRepositoryContract   $estoqueRepository,
        protected PagamentoRepositoryContract $pagamentoRepository,
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

            $this->pagamentoRepository->createPagamento($pedido);

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

            $estoqueDisponivel = $estoqueItem->produto->cardapio
                ->firstWhere('unidade_id', $pedidoDTO->unidade_id);

            if (!$estoqueDisponivel->disponivel) {
                throw EstoqueException::ProdutoIndisponivel();
            };

            if ($estoqueItem->quantidade < $item->quantidade) {
                throw EstoqueException::EstoqueInsuficiente();
            }

            $subtotal += $item->quantidade * $estoqueItem->produto->preco_base;
        }

        $total = $subtotal - $desconto;
        return array($subtotal, $desconto, $estoqueMap, $total);
    }

    public function listForCanal(?CanalPedido $canalPedido, ?OrderStatus $status)
    {
        return $this->pedidoRepository->filtro($canalPedido, $status);
    }

    /**
     * @throws InvalidOrderStatusTransitionException
     */
    public function editPedido(Pedido $pedido, Request $request): Pedido
    {
        $statusNovo = OrderStatus::from($request['status']);
        $statusAtual = $pedido->status;

        if (!$statusAtual->podeTransicionarPara($statusNovo)) {
            throw InvalidOrderStatusTransitionException::StatusNaoTransicionado();
        }

        if ($statusNovo == OrderStatus::Cancelado) {
            $motivo_cancelamento = $request['motivo_cancelamento'];

            return $this->pedidoRepository->cancelamentoPedido($pedido, $statusNovo, $motivo_cancelamento);
        }
        return $this->pedidoRepository->updateStatus($pedido, $statusNovo);
    }
}
