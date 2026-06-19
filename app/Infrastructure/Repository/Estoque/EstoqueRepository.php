<?php

namespace App\Infrastructure\Repository\Estoque;

use App\Contracts\Repository\EstoqueRepositoryContract;
use App\DTOs\Estoque\EstoqueDTO;
use App\DTOs\Estoque\MovimentacaoEstoqueDTO;
use App\Models\Estoque;
use App\Models\EstoqueMovimentacao;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class EstoqueRepository implements EstoqueRepositoryContract
{

    /**
     * @throws Throwable
     */
    public function save(EstoqueDTO $estoqueDTO): Estoque
    {
        return DB::transaction(function () use ($estoqueDTO) {
            return Estoque::create($estoqueDTO->toArray());
        });
    }

    /**
     * @throws Throwable
     */
    public function find(int $estoque_id): Estoque
    {
        return DB::transaction(function () use ($estoque_id) {
            return Estoque::find($estoque_id);
        });
    }

    public function findByUnidade(Unidade $unidade): Collection
    {
        return Estoque::with(['unidade', 'produto'])
            ->where('unidade_id', $unidade->id)
            ->get();
    }

    public function createMovimentacao(MovimentacaoEstoqueDTO $estoqueDTO): EstoqueMovimentacao
    {
        return EstoqueMovimentacao::create($estoqueDTO->toArray());
    }
}
