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
            self::AguardandoPagamento => 'Aguardando Pagamento',
            self::Pago => 'Pago',
            self::EmPreparo => 'Em Preparo',
            self::Pronto => 'Pronto',
            self::Entregue => 'Entregue',
            self::Cancelado => 'Cancelado',
        };
    }
}
