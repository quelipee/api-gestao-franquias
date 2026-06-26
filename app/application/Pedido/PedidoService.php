<?php

namespace App\application\Pedido;

use App\Contracts\Repository\EstoqueRepositoryContract;
use App\Contracts\Repository\PagamentoRepositoryContract;
use App\Contracts\Repository\PedidoRepositoryContract;
use App\Contracts\Repository\UnidadeRepositoryContract;
use App\Contracts\Services\AuditoriaServiceContract;
use App\Contracts\Services\FidelizacaoServiceContract;
use App\Contracts\Services\PedidoServiceContract;
use App\DTOs\Pedido\PedidoDTO;
use App\Enums\AuditoriaAcao;
use App\Enums\AuditoriaEntidade;
use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Exceptions\EstoqueException;
use App\Exceptions\InvalidOrderStatusTransitionException;
use App\Exceptions\UnidadeException;
use App\Exceptions\UnidadeProdutoException;
use App\Models\Fidelizacao;
use App\Models\LogAuditoria;
use App\Models\Pedido;
use App\Models\User;
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
        protected FidelizacaoServiceContract  $fidelizacaoService,
        protected AuditoriaServiceContract    $auditoriaService,
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
            $this->auditoriaService->registrar(
                user_id: $pedido->user_id,
                acao: AuditoriaAcao::PedidoCriado,
                entidade: AuditoriaEntidade::Pedido,
                entidadeId: $pedido->id,
                dadosNovos: $pedido->toArray()
            );

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
        $status = [
            'status_novo' => OrderStatus::from($request['status']),
            'status_atual' => $pedido->status
        ];
        $statusNovo = OrderStatus::from($request['status']);
        $statusAtual = $pedido->status;

        $cliente = User::find($pedido->user_id);

        if (!$status['status_atual']->podeTransicionarPara($status['status_novo'])) {
            throw InvalidOrderStatusTransitionException::StatusNaoTransicionado();
        }

        if ($status['status_novo'] === OrderStatus::Cancelado) {
            return $this->cancelarPedido($pedido, $request, $status);
        }

        $updateStatus = $this->pedidoRepository->updateStatus($pedido, $status['status_novo']);

        $this->auditoriaService->registrar(
            user_id: auth()->id(),
            acao: AuditoriaAcao::StatusAtualizado,
            entidade: AuditoriaEntidade::Pedido,
            entidadeId: $pedido->id,
            dadosAnteriores: ['status' => $status['status_atual']],
            dadosNovos: ['status' => $status['status_novo']],
        );

        if ($updateStatus->status == OrderStatus::Entregue && $cliente->consentimento_lgpd) {
            $this->fidelizacaoService->creditarPontos($pedido);
        }

        return $updateStatus;
    }

    private function cancelarPedido(Pedido $pedido, Request $request, array $status): Pedido
    {
        $motivoCancelamento = $request->input('motivo_cancelamento');

        $pedido_cancelado = $this->pedidoRepository->cancelamentoPedido(
            $pedido,
            OrderStatus::Cancelado,
            $motivoCancelamento
        );

        $this->auditoriaService->registrar(
            user_id: auth()->id(),
            acao: AuditoriaAcao::PedidoCancelado,
            entidade: AuditoriaEntidade::Pedido,
            entidadeId: $pedido->id,
            dadosAnteriores: ['status' => $status['status_atual']],
            dadosNovos: [
                'status' => $status['status_novo'],
                'motivo_cancelamento' => $motivoCancelamento,
            ],
        );

        return $pedido_cancelado;
    }
}
