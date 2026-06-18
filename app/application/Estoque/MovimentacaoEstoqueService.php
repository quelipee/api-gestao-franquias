<?php

namespace App\application\Estoque;

use App\Contracts\Repository\EstoqueRepositoryContract;
use App\Contracts\Services\MovimentacaoEstoqueServiceContract;
use App\DTOs\Estoque\MovimentacaoEstoqueDTO;
use App\Enums\TipoMovimentacaoEstoque;
use App\Models\Estoque;
use App\Models\EstoqueMovimentacao;
use Illuminate\Support\Facades\DB;

class MovimentacaoEstoqueService implements MovimentacaoEstoqueServiceContract
{
    public function __construct(
        protected EstoqueRepositoryContract $repository
    )
    {
    }

    public function save(MovimentacaoEstoqueDTO $estoqueDTO): EstoqueMovimentacao
    {
        return DB::transaction(function () use ($estoqueDTO) {
            $estoque = $this->repository->find($estoqueDTO->estoque_id);

            $quantidade = $estoqueDTO->quantidade;

            if ($estoqueDTO->tipo === TipoMovimentacaoEstoque::SAIDA) {
                if ($estoque->quantidade < $quantidade) {
                    throw new \Exception('estoque insuficiente');
                }
                $estoque->quantidade -= $quantidade;
            }

            if ($estoqueDTO->tipo === TipoMovimentacaoEstoque::ENTRADA) {
                $estoque->quantidade += $quantidade;
            }

            $estoque->save();

            return $this->repository->createMovimentacao($estoqueDTO);
        });
    }
}
