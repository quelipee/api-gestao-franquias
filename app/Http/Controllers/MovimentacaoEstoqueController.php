<?php

namespace App\Http\Controllers;

use App\Contracts\Services\MovimentacaoEstoqueServiceContract;
use App\DTOs\Estoque\MovimentacaoEstoqueDTO;
use App\Http\Requests\EstoqueMovimentacaoRequest;
use App\Models\Estoque;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MovimentacaoEstoqueController extends Controller
{
    public function __construct(
        protected MovimentacaoEstoqueServiceContract $service
    )
    {
    }

    public function store(EstoqueMovimentacaoRequest $request)
    {
        $estoque = $this->service->save(MovimentacaoEstoqueDTO::fromRequest($request));

        return response()->json([
            'message' => 'Estoque atualizado com sucesso!',
            'data' => $estoque
        ], ResponseAlias::HTTP_CREATED);
    }
}
