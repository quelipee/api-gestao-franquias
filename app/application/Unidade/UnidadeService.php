<?php

namespace App\application\Unidade;

use App\Contracts\Repository\UnidadeRepositoryContract;
use App\Contracts\Services\UnidadeServiceContract;
use App\DTOs\Unidade\UnidadeDTO;
use App\Exceptions\UnidadeException;
use App\Http\Resources\UnidadeClientResource;
use App\Models\Unidade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UnidadeService implements UnidadeServiceContract
{
    public function __construct(
        protected UnidadeRepositoryContract $repository
    )
    {
    }

    public function create(UnidadeDTO $unidadeDTO): Unidade
    {
        return $this->repository->save($unidadeDTO);
    }

    public function list(int $perPage): LengthAwarePaginator
    {
        return $this->repository->getList($perPage);
    }

    /**
     * @throws UnidadeException
     */
    public function unidadeAtivo(Unidade $unidade)
    {
        if (!$unidade) {
            throw UnidadeException::UnidadeInvalida();
        }

        if (!$unidade->ativo) {
            throw UnidadeException::UnidadeInativa($unidade);
        }
         return UnidadeClientResource::make($unidade);
    }
}
