<?php

namespace App\Contracts\Repository;

use App\DTOs\Produto\ProdutoDataDTO;
use App\Models\Produto;

interface ProdutoRepositoryContract
{
    public function create(ProdutoDataDTO $dto): Produto;

    public function update(Produto $produto, ProdutoDataDTO $produtoUpdated) : Produto;
}
