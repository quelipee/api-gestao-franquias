<?php

namespace App\application\UnidadeProduto;

use App\Contracts\Services\UnidadeProdutoServiceContract;
use App\DTOs\UnidadeProduto\UnidadeProdutoDTO;
use App\Exceptions\UnidadeProdutoException;
use App\Models\Produto;
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

    /**
     * @throws UnidadeProdutoException
     */
    public function detach(Unidade $unidade, Produto $produto): void
    {
        if (!$unidade->produtos()->whereKey($produto->id)->exists()) {
            throw UnidadeProdutoException::ProdutoNaoVinculado();
        }
        //TODO TENHO QUE VERIFICAR DEPOIS
        $unidade->produtos()->detach($produto);
    }

    /**
     * @throws UnidadeProdutoException
     */
    public function listProdutoUnidade(Unidade $unidade): Unidade
    {
        $unidade->load(['produtos' => function ($query) {
            $query->wherePivot('disponivel', true);
        }]);

        return $unidade;
    }
}
