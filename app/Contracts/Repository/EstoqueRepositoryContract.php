<?php

namespace App\Contracts\Repository;

use App\DTOs\Estoque\EstoqueDTO;
use App\DTOs\Estoque\MovimentacaoEstoqueDTO;
use App\Models\Estoque;
use App\Models\EstoqueMovimentacao;

interface EstoqueRepositoryContract
{
    public function save(EstoqueDTO $estoqueDTO): Estoque;

    public function find(int $estoque_id): Estoque;

    public function createMovimentacao(MovimentacaoEstoqueDTO $estoqueDTO): EstoqueMovimentacao;
}
