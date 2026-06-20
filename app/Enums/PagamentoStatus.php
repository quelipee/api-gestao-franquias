<?php

namespace App\Enums;

enum PagamentoStatus: string
{
    case Aprovado = 'APROVADO';
    case Negado = 'NEGADO';
    case Pendente = 'PENDENTE';

    public function label(): string
    {
        return match ($this) {
            self::Aprovado => 'Aprovado',
            self::Negado => 'Negado',
        };
    }
}
