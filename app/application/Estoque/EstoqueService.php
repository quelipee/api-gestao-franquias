<?php

namespace App\application\Estoque;

use App\Contracts\Repository\EstoqueRepositoryContract;
use App\Contracts\Services\EstoqueServiceContract;
use App\DTOs\Estoque\EstoqueDTO;
use App\Models\Estoque;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Collection;

class EstoqueService implements EstoqueServiceContract
{
    public function __construct(
        protected EstoqueRepositoryContract $estoqueRepository
    )
    {
    }

    public function addProduto(EstoqueDTO $estoqueDTO): Estoque
    {
        return $this->estoqueRepository->save($estoqueDTO);
    }

    public function view(Unidade $unidade): Collection
    {
        return $this->estoqueRepository->findByUnidade($unidade);
    }
}
