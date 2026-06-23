<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PedidoServiceContract;
use App\DTOs\Pedido\PedidoDTO;
use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Http\Requests\PedidoRequest;
use App\Models\Pedido;
use App\Policies\PedidoPolicy;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PedidoController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected PedidoServiceContract $service,
        protected PedidoPolicy          $policy
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

    public function index(Request $request)
    {
        $pedidos = $this->service->listForCanal(
            CanalPedido::tryFrom($request->query('canal_pedido')),
            OrderStatus::tryFrom($request->query('status'))
        );

        return response()->json([
            'message' => 'Lista de Pedidos!',
            'data' => $pedidos
        ], ResponseAlias::HTTP_OK);
    }

    public function update(Pedido $pedido, Request $request)
    {
        Gate::authorize('update', [$pedido, OrderStatus::from($request->status)]);
        $pedido = $this->service->editPedido($pedido, $request);

        return response()->json([
            'message' => 'Status atualizado com sucesso!',
            'data' => $pedido
        ], ResponseAlias::HTTP_OK);
    }
}
