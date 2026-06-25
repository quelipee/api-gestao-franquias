<?php

namespace App\Enums;

enum TipoPagamento: string
{
    case PIX = 'PIX';
    case CARTAO_CREDITO = 'CARTAO_CREDITO';
    case CARTAO_DEBITO = 'CARTAO_DEBITO';
    case DINHEIRO = 'DINHEIRO';
    case MOCK = 'MOCK';

    /**
     * Retorna um rótulo amigável para exibição no front-end.
     */
    public function label(): string
    {
        return match ($this) {
            self::PIX => 'Pix',
            self::CARTAO_CREDITO => 'Cartão de Crédito',
            self::CARTAO_DEBITO => 'Cartão de Débito',
            self::DINHEIRO => 'Dinheiro em Espécie',
            self::MOCK => 'PagamentoController Simulado (Mock)',
        };
    }
}
