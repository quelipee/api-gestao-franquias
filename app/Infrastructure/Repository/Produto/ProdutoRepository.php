<?php

namespace App\Infrastructure\Repository\Produto;

use App\Contracts\Repository\ProdutoRepositoryContract;
use App\DTOs\Produto\ProdutoDataDTO;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProdutoRepository implements ProdutoRepositoryContract
{
    public function create(ProdutoDataDTO $dto): Produto
    {
        return Produto::create([
            'nome' => $dto->nome,
            'descricao' => $dto->descricao,
            'preco' => $dto->preco,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(Produto $produto, ProdutoDataDTO $produtoUpdated) : Produto
    {
        return DB::transaction(function () use ($produto, $produtoUpdated) {
            $produto->fill([
               'nome' => $produtoUpdated->nome,
               'descricao' => $produtoUpdated->descricao,
               'preco' => $produtoUpdated->preco,
            ]);

            $produto->save();

            return $produto;
        });
    }
}
