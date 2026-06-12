<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ProdutoServiceContract;
use App\DTOs\Produto\ProdutoDataDTO;
use App\Http\Requests\ProdutoStoreRequest;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProdutoController extends Controller
{
    public function __construct(
        protected ProdutoServiceContract $produtoService
    )
    {
    }

    public function index()
    {
        return Produto::all();
    }

    public function show(Produto $produto)
    {
        return $produto->toArray();
    }

    public function store(ProdutoStoreRequest $request) : JsonResponse
    {
        $produto = $this->produtoService->create(ProdutoDataDTO::fromRequest($request));

        return response()->json([
            'message' => 'Produto cadastrado com sucesso!',
            'data' => $produto->toArray()
        ], ResponseAlias::HTTP_CREATED);

    }

    public function update(Produto $produto, ProdutoStoreRequest $request): JsonResponse
    {
        $produtoAtualizado = $this->produtoService
            ->update(ProdutoDataDTO::fromRequest($request), $produto);

        return response()->json([
            'message' => 'Produto atualizado com sucesso!',
            'data'    => $produtoAtualizado
        ], ResponseAlias::HTTP_OK);
    }

    public function destroy($id)
    {

    }
}
