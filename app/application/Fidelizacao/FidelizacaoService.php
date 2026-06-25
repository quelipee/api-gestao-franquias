<?php

namespace App\application\Fidelizacao;

use App\Contracts\Repository\PedidoRepositoryContract;
use App\Contracts\Services\FidelizacaoServiceContract;
use App\Enums\TipoTransacaoFidelizacao;
use App\Exceptions\FidelidadeException;
use App\Models\Fidelizacao;
use App\Models\Pedido;
use App\Models\TransacaoFidelizacao;
use App\Models\User;

class FidelizacaoService implements FidelizacaoServiceContract
{
    public function __construct(
        protected PedidoRepositoryContract $pedidoRepository,
    )
    {
    }

    private const float VALOR_POR_PONTO = 0.10;

    public function creditarPontos(Pedido $pedido): void
    {
        $fidelizacao = $this->pedidoRepository->createFidelizacao($pedido->user_id);

        if (!$fidelizacao || !$fidelizacao->ativo) {
            return;
        }
        $pontos = (int)floor($pedido->total / 10);
        $fidelizacao->increment('pontos_saldo', $pontos);
        $fidelizacao->increment('pontos_acumulados_total', $pontos);

        TransacaoFidelizacao::create([
            'fidelizacao_id' => $fidelizacao->id,
            'pedido_id' => $pedido->id,
            'tipo' => TipoTransacaoFidelizacao::Acumulo,
            'pontos' => $pontos,
            'descricao' => 'Pontos do pedido ' . $pedido->numero_pedido,
        ]);
    }

    public function infoSaldo(): Fidelizacao
    {
        return $this->pedidoRepository->createFidelizacao(auth()->id());
    }

    /**
     * @throws FidelidadeException
     */
    public function resgatarPontos(Pedido $pedido, mixed $pontos): void
    {
        $fidelizacao = Fidelizacao::query()
            ->where('user_id', auth()->id())->firstOrFail();

        if (!$fidelizacao || $fidelizacao->pontos_saldo < $pontos) {
            throw FidelidadeException::SaldoInsuficiente();
        }

        $desconto = $pontos * self::VALOR_POR_PONTO;

        $fidelizacao->decrement('pontos_saldo', $pontos);
        $fidelizacao->increment('pontos_resgatados_total', $pontos);

        $this->pedidoRepository->updateFidelidade($pedido, [
            'desconto' => $pedido->desconto + $desconto,
            'total' => $pedido->subtotal - ($pedido->desconto + $desconto),
        ]);

        TransacaoFidelizacao::create([
            'fidelizacao_id' => $fidelizacao->id,
            'pedido_id' => $pedido->id,
            'tipo' => TipoTransacaoFidelizacao::Resgate,
            'pontos' => -$pontos,
            'descricao' => 'Resgate aplicado no pedido ' . $pedido->numero_pedido,
        ]);
    }
}
