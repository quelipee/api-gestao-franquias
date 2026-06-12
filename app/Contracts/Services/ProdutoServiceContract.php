<?php

namespace App\Contracts\Services;

use App\DTOs\Produto\ProdutoDataDTO;
use App\Models\Produto;

interface ProdutoServiceContract
{
    public function create(ProdutoDataDTO $produtoDTO) : Produto;

    public function update(ProdutoDataDTO $produtoUpdated, Produto $produto) : Produto;
}
