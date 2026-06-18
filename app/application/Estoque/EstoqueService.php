<?php

namespace App\application\Estoque;

use App\Contracts\Repository\EstoqueRepositoryContract;
use App\Contracts\Services\EstoqueServiceContract;
use App\DTOs\Estoque\EstoqueDTO;
use App\Models\Estoque;

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
}
