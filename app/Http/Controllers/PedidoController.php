<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PedidoServiceContract;
use App\DTOs\Pedido\PedidoDTO;
use App\Http\Requests\PedidoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PedidoController extends Controller
{
    public function __construct(
        protected PedidoServiceContract $service
    )
    {
    }

    public function store(PedidoRequest $request)
    {
        $pedido = $this->service->create(PedidoDTO::fromRequest($request));

        return response()->json([
            'message' => 'Pedido feito com sucesso!',
            'pedido' => $pedido
        ], ResponseAlias::HTTP_CREATED);
    }
}
