<?php

namespace App\Enums;

enum OrderStatus: string
{
    case AguardandoPagamento = 'AGUARDANDO_PAGAMENTO';
    case Pago = 'PAGO';
    case EmPreparo = 'EM_PREPARO';
    case Pronto = 'PRONTO';
    case Entregue = 'ENTREGUE';
    case Cancelado = 'CANCELADO';

    public function label(): string
    {
        return match ($this) {
            self::AguardandoPagamento => 'Aguardando PagamentoRepositoryContract',
            self::Pago => 'Pago',
            self::EmPreparo => 'Em Preparo',
            self::Pronto => 'Pronto',
            self::Entregue => 'Entregue',
            self::Cancelado => 'Cancelado',
        };
    }

    public function ordem(): int
    {
        return match ($this) {
            self::AguardandoPagamento => 0,
            self::Pago => 1,
            self::EmPreparo => 2,
            self::Pronto => 3,
            self::Entregue => 4,
            self::Cancelado => -1, // não entra na ordem normal
        };
    }

    public function podeTransicionarPara(OrderStatus $novoStatus): bool
    {
        if ($novoStatus === self::Cancelado) {
            return !in_array($this, [self::Entregue, self::Cancelado], true);
        }

        if (in_array($this, [self::Entregue, self::Cancelado], true)) {
            return false;
        }

        return $novoStatus->ordem() > $this->ordem();
    }
}
