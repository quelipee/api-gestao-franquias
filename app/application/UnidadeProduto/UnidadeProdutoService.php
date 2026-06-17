<?php

namespace App\application\UnidadeProduto;

use App\Contracts\Services\UnidadeProdutoServiceContract;
use App\DTOs\UnidadeProduto\UnidadeProdutoDTO;
use App\Models\Unidade;

class UnidadeProdutoService implements UnidadeProdutoServiceContract
{

    public function attach(UnidadeProdutoDTO $unidadeProdutoDTO, Unidade $unidade): Unidade
    {
        $unidade->produtos()->attach($unidadeProdutoDTO->produto_id,
            ['disponivel' => $unidadeProdutoDTO->disponivel ?? true]);

        $unidade->load('produtos');

        return $unidade;
    }
}
