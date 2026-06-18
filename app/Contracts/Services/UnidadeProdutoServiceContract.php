<?php

namespace App\Contracts\Services;

use App\DTOs\UnidadeProduto\UnidadeProdutoDTO;
use App\Models\Produto;
use App\Models\Unidade;

interface UnidadeProdutoServiceContract
{
    public function attach(UnidadeProdutoDTO $unidadeProdutoDTO, Unidade $unidade): Unidade;

    public function detach(Unidade $unidade, Produto $produto): void;

    public function listProdutoUnidade(Unidade $unidade): Unidade;
}
