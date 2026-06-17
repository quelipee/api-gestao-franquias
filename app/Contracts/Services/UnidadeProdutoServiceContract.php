<?php

namespace App\Contracts\Services;

use App\DTOs\UnidadeProduto\UnidadeProdutoDTO;
use App\Models\Unidade;

interface UnidadeProdutoServiceContract
{
    public function attach(UnidadeProdutoDTO $unidadeProdutoDTO, Unidade $unidade): Unidade;
}
