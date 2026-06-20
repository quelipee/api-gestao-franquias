<?php

namespace App\application\UnidadeProduto;

use App\Contracts\Services\UnidadeProdutoServiceContract;
use App\DTOs\UnidadeProduto\UnidadeProdutoDTO;
use App\Exceptions\UnidadeProdutoException;
use App\Models\CardapioUnidade;
use App\Models\Produto;
use App\Models\Unidade;

class UnidadeProdutoService implements UnidadeProdutoServiceContract
{

    /**
     * @throws UnidadeProdutoException
     */
    public function attach(UnidadeProdutoDTO $unidadeProdutoDTO, Unidade $unidade): Unidade
    {
        $exists = CardapioUnidade::query()
            ->where('produto_id', $unidadeProdutoDTO->produto_id)
            ->where('unidade_id', $unidade->id)
            ->exists();
        if ($exists) {
            throw UnidadeProdutoException::ProdutoJaVinculado();
        }

        CardapioUnidade::create([
            'produto_id' => $unidadeProdutoDTO->produto_id,
            'unidade_id' => $unidade->id,
            'disponivel' => $unidadeProdutoDTO->disponivel ?? true,
        ]);

        return $unidade->load('produtos');
    }

    /**
     * @throws UnidadeProdutoException
     */
    public function detach(Unidade $unidade, Produto $produto): void
    {
        $vinculo = CardapioUnidade::query()
            ->where('produto_id',$produto->id)
            ->where('unidade_id',$unidade->id)
            ->first();
        if (!$vinculo) {
            throw UnidadeProdutoException::ProdutoNaoVinculado();
        }

        $vinculo->delete();
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
