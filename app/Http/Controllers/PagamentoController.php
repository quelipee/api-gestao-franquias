<?php

namespace App\Http\Controllers;

use App\Contracts\Services\PagamentoServiceContract;
use App\DTOs\Pagamento\ProcessarPagamentoDTO;
use App\Http\Requests\ProcessarPagamentoRequest;
use App\Models\Pagamento;
use App\Models\Pedido;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PagamentoController
{
    use AuthorizesRequests;

    public function __construct(
        protected PagamentoServiceContract $pagamentoService
    )
    {
    }

    public function processar(Pedido $pedido, ProcessarPagamentoRequest $request)
    {
        Gate::authorize('create', [Pagamento::class, $pedido]);

        $pagamento = $this->pagamentoService->processarPagamento($pedido, ProcessarPagamentoDTO::fromRequest($request));

        return response()->json([
            'message' => $pagamento->aprovado_em ? 'Pagamento realizado com sucesso.' : 'Pagamento recusado.',
            'data' => $pagamento
        ], ResponseAlias::HTTP_OK);
    }
}
