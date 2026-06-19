<?php

namespace App\Contracts\Repository;

use App\DTOs\Estoque\EstoqueDTO;
use App\DTOs\Estoque\MovimentacaoEstoqueDTO;
use App\Models\Estoque;
use App\Models\EstoqueMovimentacao;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Collection;

interface EstoqueRepositoryContract
{
    public function save(EstoqueDTO $estoqueDTO): Estoque;

    public function find(int $estoque_id): Estoque;

    public function findByUnidade(Unidade $unidade): Collection;

    public function createMovimentacao(MovimentacaoEstoqueDTO $estoqueDTO): EstoqueMovimentacao;
}
