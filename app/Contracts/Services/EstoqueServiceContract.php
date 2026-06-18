<?php

namespace App\Contracts\Services;

use App\DTOs\Estoque\EstoqueDTO;
use App\Models\Estoque;

interface EstoqueServiceContract
{

    public function addProduto(EstoqueDTO $estoqueDTO): Estoque;
}
