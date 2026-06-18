<?php

namespace App\Http\Controllers;

use App\Contracts\Services\EstoqueServiceContract;
use App\DTOs\Estoque\EstoqueDTO;
use App\Http\Requests\EstoqueRequest;
use App\Models\Estoque;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EstoqueController extends Controller
{
    public function __construct(
        protected EstoqueServiceContract $service
    )
    {
    }

    public function store(EstoqueRequest $request)
    {
        $ProdutoEstoque = $this->service->addProduto(EstoqueDTO::fromRequest($request));

        return response()->json([
            'message' => "Produto cadastrado com sucesso!",
            'data' => $ProdutoEstoque
        ], ResponseAlias::HTTP_CREATED);
    }

}
