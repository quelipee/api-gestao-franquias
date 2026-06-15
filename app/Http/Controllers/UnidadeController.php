<?php

namespace App\Http\Controllers;

use App\Contracts\Services\UnidadeServiceContract;
use App\DTOs\Unidade\UnidadeDTO;
use App\Http\Requests\UnidadeRequest;
use App\Models\Unidade;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UnidadeController extends Controller
{
    public function __construct(
        protected UnidadeServiceContract $unidadeService
    )
    {
    }

    public function index(Request $request)
    {
        $perPage = $request->integer('per_page', 5);
        $list = $this->unidadeService->list($perPage);

        return response()->json([
            'message' => 'lista de todas as unidades',
            'data' => $list
        ]);
    }

    public function show(Unidade $unidade)
    {
        $unidade = $this->unidadeService->unidadeAtivo($unidade);

        return response()->json([
            'message' => 'unidade ativa',
            'data' => $unidade
        ], ResponseAlias::HTTP_OK);
    }

    public function store(UnidadeRequest $request)
    {
        $unidade = $this->unidadeService->create(UnidadeDTO::fromRequest($request));

        return response()->json([
            'message' => 'Unidade cadastrado com sucesso!',
            'data' => $unidade
        ], ResponseAlias::HTTP_CREATED);
    }

    public function update(UnidadeRequest $request, Unidade $unidade)
    {
        $unidadeAtualizada = $this->unidadeService->update(
            UnidadeDTO::fromRequest($request), $unidade
        );

        return response()->json([
            'message' => 'Unidade atualizado com sucesso!',
            'data' => $unidadeAtualizada
        ], ResponseAlias::HTTP_OK);
    }

    public function destroy(Unidade $unidade)
    {
        $this->unidadeService->delete($unidade);

        return response()->noContent(ResponseAlias::HTTP_NO_CONTENT);
    }
}
