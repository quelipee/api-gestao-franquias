<?php

namespace App\Http\Controllers;

use App\Contracts\Services\EstoqueServiceContract;
use App\DTOs\Estoque\EstoqueDTO;
use App\Http\Requests\EstoqueRequest;
use App\Models\Unidade;
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

    public function index(Unidade $unidade)
    {
        $estoque = $this->service->view($unidade);

        return response()->json([
            'message' => "Produtos cadastrados",
            'data' => $estoque
        ], ResponseAlias::HTTP_OK);
    }

}
