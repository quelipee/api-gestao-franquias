<?php

namespace App\Infrastructure\Repository\Produto;

use App\Contracts\Repository\ProdutoRepositoryContract;
use App\DTOs\Produto\ProdutoDataDTO;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProdutoRepository implements ProdutoRepositoryContract
{
    /**
     * @throws Throwable
     */
    public function save(ProdutoDataDTO $dto): Produto
    {
        return Db::transaction(function () use ($dto) {
            return Produto::create($dto->toArray());
        });
    }

    /**
     * @throws Throwable
     */
    public function update(Produto $produto, ProdutoDataDTO $produtoUpdated) : Produto
    {
        return DB::transaction(function () use ($produto, $produtoUpdated) {
            $produto->update($produtoUpdated->toArray());
            return $produto;
        });
    }
}
