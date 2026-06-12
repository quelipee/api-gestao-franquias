<?php

namespace App\application\Produto;

use App\Contracts\Repository\ProdutoRepositoryContract;
use App\Contracts\Services\ProdutoServiceContract;
use App\DTOs\Produto\ProdutoDataDTO;
use App\Models\Produto;

class ProdutoService implements ProdutoServiceContract
{
    public function __construct(
        private ProdutoRepositoryContract $repository
    )
    {
    }

    public function create(ProdutoDataDTO $produtoDTO): Produto
    {
        return $this->repository->create($produtoDTO);
    }

    public function update(ProdutoDataDTO $produtoUpdated, Produto $produto) : Produto
    {
        return $this->repository->update($produto, $produtoUpdated);
    }
}
