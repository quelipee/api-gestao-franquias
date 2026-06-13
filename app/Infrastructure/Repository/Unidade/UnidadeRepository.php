<?php

namespace App\Infrastructure\Repository\Unidade;

use App\Contracts\Repository\UnidadeRepositoryContract;
use App\DTOs\Unidade\UnidadeDTO;
use App\Models\Unidade;
use Illuminate\Support\Facades\DB;

class UnidadeRepository implements UnidadeRepositoryContract
{

    /**
     * @throws \Throwable
     */
    public function save(UnidadeDTO $unidadeDTO) : Unidade
    {
        return DB::transaction(function () use ($unidadeDTO) {
            return Unidade::create($unidadeDTO->toArray());
        });
    }
}
