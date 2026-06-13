<?php

namespace App\application\Unidade;

use App\Contracts\Repository\UnidadeRepositoryContract;
use App\Contracts\Services\UnidadeServiceContract;
use App\DTOs\Unidade\UnidadeDTO;
use App\Models\Unidade;

class UnidadeService implements UnidadeServiceContract
{
    public function __construct(
        protected UnidadeRepositoryContract $repository
    )
    {
    }

    public function create(UnidadeDTO $unidadeDTO) : Unidade
    {
        return $this->repository->save($unidadeDTO);
    }

    public function list(): array
    {
        dd(Unidade::all()->toArray());
    }
}
