<?php

use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\FidelizacaoController;
use App\Http\Controllers\MovimentacaoEstoqueController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\UnidadeController;
use App\Http\Controllers\UnidadeProdutoController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('guest:sanctum')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('unidades', [UnidadeController::class, 'index']);
    Route::get('unidades/{unidade}', [UnidadeController::class, 'show']);

    Route::get('unidades/{unidade}/produtos', [UnidadeProdutoController::class, 'index']);

    Route::get('produtos', [ProdutoController::class, 'index']);
    Route::get('produtos/{produto}', [ProdutoController::class, 'show']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/unidades', [UnidadeController::class, 'store'])->middleware(['role:admin']);
    Route::put('/unidades/{unidade}', [UnidadeController::class, 'update'])->middleware(['role:admin']);
    Route::delete('/unidades/{unidade}', [UnidadeController::class, 'destroy'])->middleware(['role:admin']);

    Route::post('/unidades/{unidade}/produtos', [UnidadeProdutoController::class, 'store'])->middleware(['role:admin,gerente']);
    Route::delete('/unidades/{unidade}/produtos/{produto}', [UnidadeProdutoController::class, 'destroy'])->middleware(['role:admin,gerente']);
});

Route::middleware(['auth:sanctum', 'role:admin,gerente'])->group(function () {
    Route::post('produtos', [ProdutoController::class, 'store']);
    Route::put('produtos/{produto}', [ProdutoController::class, 'update']);
    Route::delete('produtos/{produto}', [ProdutoController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:admin,gerente'])->group(function () {
    Route::get('/estoque/{unidade}', [EstoqueController::class, 'index']);
    Route::post('/estoque', [EstoqueController::class, 'store']);

    Route::post('/estoque/movimentacao', [MovimentacaoEstoqueController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'role:cliente'])->group(function () {
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::get('/pedidos', [PedidoController::class, 'index']);

    Route::get('fidelizacoes/saldo', [FidelizacaoController::class, 'saldo']);
    Route::post('/pedidos/{pedido}/fidelidade/resgate', [FidelizacaoController::class, 'resgate']);
});

Route::middleware(['auth:sanctum', 'role:cliente'])->group(function () {
    Route::post('/pedidos/{pedido}/pagamento', [PagamentoController::class, 'processar']);
});

Route::middleware(['auth:sanctum', 'role:cozinha,gerente,cliente,atendente'])->group(function () {
    Route::patch('/pedidos/{pedido}/status', [PedidoController::class, 'update']);
});
