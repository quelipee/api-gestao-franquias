<?php

namespace App\Contracts\Services;

use App\Models\Pedido;
use App\Models\User;

interface FidelizacaoServiceContract
{
    public function creditarPontos(Pedido $pedido): void;

    public function infoSaldo();

    public function resgatarPontos(Pedido $pedido, mixed $pontos);
}
