<?php

namespace App\Enums;

enum TipoMovimentacaoEstoque: string
{
    case ENTRADA = 'ENTRADA';
    case SAIDA = 'SAIDA';
}
