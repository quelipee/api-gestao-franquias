<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UnidadeProdutoServiceContract;
use App\DTOs\UnidadeProduto\UnidadeProdutoDTO;
use App\Http\Requests\UnidadeProdutoRequest;
use App\Models\Unidade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UnidadeProdutoController extends Controller
{
    public function __construct(
        protected UnidadeProdutoServiceContract $service
    )
    {
    }

    public function store(UnidadeProdutoRequest $request, Unidade $unidade): JsonResponse
    {
        $unidade = $this->service->attach(UnidadeProdutoDTO::fromRequest($request), $unidade);

        return response()->json([
            'message' => 'Unidade adicionada com sucesso!',
            'data' => $unidade
        ], ResponseAlias::HTTP_CREATED);
    }
}
