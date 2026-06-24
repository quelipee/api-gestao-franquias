<?php

namespace App\application\Pagamento;

use App\Contracts\Repository\PagamentoRepositoryContract;
use App\Contracts\Repository\PedidoRepositoryContract;
use App\Contracts\Services\PagamentoServiceContract;
use App\DTOs\Pagamento\ProcessarPagamentoDTO;
use App\Enums\OrderStatus;
use App\Enums\PagamentoStatus;
use App\Exceptions\InvalidOrderStatusTransitionException;
use App\Exceptions\PagamentoException;
use App\Models\Pagamento;
use App\Models\Pedido;

class MockService implements PagamentoServiceContract
{
    public function __construct(
        protected PagamentoRepositoryContract $pagamentoRepository,
        protected PedidoRepositoryContract    $pedidoRepository,
    )
    {
    }

    /**
     * @throws PagamentoException
     * @throws InvalidOrderStatusTransitionException
     */
    public function processarPagamento(Pedido $pedido, ProcessarPagamentoDTO $dto): Pagamento
    {
        $pagamentoPedido = $pedido->pagamentos()
            ->where('status', PagamentoStatus::Pendente)
            ->first();

        if (!$pagamentoPedido) {
            throw PagamentoException::PagamentoPendenteNaoEncontrado();
        }

        $aprovado = ($dto->simular_resultado ?? PagamentoStatus::Aprovado) === PagamentoStatus::Aprovado;

        $pagamentoStatus = $this->pagamentoRepository->atualizarStatus($pagamentoPedido, $aprovado ? PagamentoStatus::Aprovado : PagamentoStatus::Negado,
            $this->getDataPagamento($dto->forma_pagamento, $aprovado));

        if ($pagamentoStatus->status === PagamentoStatus::Aprovado) {
            if (!$pedido->status->podeTransicionarPara(OrderStatus::Pago)) {
                throw InvalidOrderStatusTransitionException::StatusNaoTransicionado();
            }
            $this->pedidoRepository->updateStatus($pedido, OrderStatus::Pago);
        }

        return $pagamentoStatus;
    }

    /**
     * @param $forma_pagamento
     * @param bool $aprovado
     * @return array
     */
    public function getDataPagamento($forma_pagamento, bool $aprovado): array
    {
        return [
            'forma_pagamento' => $forma_pagamento,
            'gateway_status' => $aprovado ? 'succeeded' : 'declined',
            'aprovado_em' => $aprovado ? now() : null,
            'recusado_em' => $aprovado ? null : now(),
            'gateway_payload' => ['mensagem' => $aprovado ? 'Aprovado via simulador' : 'Recusado via simulador']
        ];
    }
}
