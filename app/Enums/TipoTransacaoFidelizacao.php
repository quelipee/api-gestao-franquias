<?php

namespace App\Enums;

enum TipoTransacaoFidelizacao: string
{
    case Acumulo = 'acumulo';
    case Resgate = 'resgate';
}
