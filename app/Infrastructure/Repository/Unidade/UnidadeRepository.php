<?php

namespace App\Infrastructure\Repository\Unidade;

use App\Contracts\Repository\UnidadeRepositoryContract;
use App\DTOs\Unidade\UnidadeDTO;
use App\Models\Unidade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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

    /**
     * @throws Throwable
     */
    public function update(UnidadeDTO $unidadeDTO, Unidade $unidade): Unidade
    {
        return DB::transaction(function () use ($unidade, $unidadeDTO) {
            $unidade->update($unidadeDTO->toArray());
            return $unidade;
        });
    }

    public function delete(Unidade $unidade) : bool
    {
        return DB::transaction(function () use ($unidade) {
            return $unidade->delete();
        });
    }

    public function findById(int $id): Unidade
    {
        return DB::transaction(function () use ($id) {
            return Unidade::query()->where('id',$id)->first();
        });
    }
}
