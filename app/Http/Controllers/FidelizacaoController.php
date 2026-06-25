<?php

namespace App\Http\Controllers;

use App\Contracts\Services\FidelizacaoServiceContract;
use App\Http\Resources\FidelizacaoResource;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class FidelizacaoController extends Controller
{
    public function __construct(
        protected FidelizacaoServiceContract $fidelizacaoService
    )
    {
    }

    public function saldo()
    {
        $fidelizacao = $this->fidelizacaoService->infoSaldo();

        return response()->json([
            'message' => 'Todos os pontos carregados com sucesso!',
            'data' => new FidelizacaoResource($fidelizacao),
        ], ResponseAlias::HTTP_OK);
    }

    public function resgate(Pedido $pedido, Request $request)
    {
        $this->fidelizacaoService->resgatarPontos($pedido, $request->pontos);

        return response()->json([
            'message' => 'Resgate aplicado com sucesso!',
        ]);
    }
}
