<?php

namespace App\Contracts\Repository;

use App\DTOs\Unidade\UnidadeDTO;
use App\Models\Unidade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UnidadeRepositoryContract
{
    public function save(UnidadeDTO $unidadeDTO): Unidade;

    public function getList(int $perPage): LengthAwarePaginator;

    public function update(UnidadeDTO $unidadeDTO, Unidade $unidade): Unidade;

    public function delete(Unidade $unidade): bool;

    public function findById(int $id): Unidade;
}
