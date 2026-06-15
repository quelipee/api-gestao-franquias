<?php

namespace App\Infrastructure\Repository\Unidade;

use App\Contracts\Repository\UnidadeRepositoryContract;
use App\DTOs\Unidade\UnidadeDTO;
use App\Models\Unidade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

class UnidadeRepository implements UnidadeRepositoryContract
{

    /**
     * @throws Throwable
     */
    public function save(UnidadeDTO $unidadeDTO): Unidade
    {
        return DB::transaction(function () use ($unidadeDTO) {
            return Unidade::create($unidadeDTO->toArray());
        });
    }

    /**
     * @throws Throwable
     */
    public function getList(int $perPage): LengthAwarePaginator
    {
        return DB::transaction(function () use ($perPage) {
            return Unidade::query()->paginate($perPage);
        });
    }
}
