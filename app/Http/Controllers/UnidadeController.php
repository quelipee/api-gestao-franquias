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

    public function index()
    {
        $list = $this->unidadeService->list();

        return response()->json([
            'message' => 'lista de todas as unidades',
            'data' => $list
        ]);
    }

    public function show(Unidade $unidade)
    {
        return $unidade;
    }

    public function store(UnidadeRequest $request)
    {
        $unidade = $this->unidadeService->create(UnidadeDTO::fromRequest($request));
        return response()->json([
            'message' => 'Unidade cadastrado com sucesso!',
            'data' => $unidade
        ], ResponseAlias::HTTP_CREATED);
    }
}
