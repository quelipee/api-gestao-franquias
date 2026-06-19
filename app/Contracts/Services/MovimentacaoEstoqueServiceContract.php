<?php

namespace App\Contracts\Services;

use App\DTOs\Estoque\MovimentacaoEstoqueDTO;
use App\Models\EstoqueMovimentacao;

interface MovimentacaoEstoqueServiceContract
{
    public function save(MovimentacaoEstoqueDTO $estoqueDTO): EstoqueMovimentacao;
}
