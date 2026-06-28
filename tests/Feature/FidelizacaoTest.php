<?php

namespace Tests\Feature;

use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Enums\PagamentoStatus;
use App\Enums\TipoPagamento;
use App\Enums\TipoTransacaoFidelizacao;
use App\Enums\UserRole;
use App\Models\CardapioUnidade;
use App\Models\Estoque;
use App\Models\Fidelizacao;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class FidelizacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate(UserRole $role = UserRole::CLIENTE): User
    {
        $user = User::factory()->create([
            'role' => $role,
        ]);

        $this->actingAs($user, 'sanctum');

        return $user;
    }

    protected function createUnidade(int $qtd = 5): Collection
    {
        return Unidade::factory()->count($qtd)->create();
    }

    protected function unidadeAttachProduto(Unidade $unidade, bool $disponivel = true)
    {
        $produtos = Produto::factory()->count(3)->create();

        foreach ($produtos as $produto) {
            CardapioUnidade::create([
                'produto_id' => $produto->id,
                'unidade_id' => $unidade->id,
                'disponivel' => $disponivel,
            ]);
        }

        $unidade->load('produtos');

        return $unidade;
    }

    public function createEstoque($produtos, ?Unidade $unidade): void
    {
        foreach ($produtos as $produto) {
            Estoque::factory()->create([ // cria o estoque vinculado com a unidade e o produto
                'unidade_id' => $unidade->id,
                'produto_id' => $produto['id'],
                'quantidade' => 10,
                'quantidade_minima' => 1,
            ]);
        }
    }

    protected function vincularUsuarioUnidade(User $user, Unidade $unidade): void
    {
        $unidade->users()->attach($user->id);
    }

    public function test_cliente_accumulates_points_after_pedido_entregue()
    {
        $atendente = $this->authenticate(UserRole::ATENDENTE);
        $user = User::factory()->create();

        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio->produtos, $unidade);
        $this->vincularUsuarioUnidade($atendente, $unidade);

        $fidelizacao = Fidelizacao::factory()->create([
            'user_id' => $user->id,
            'pontos_saldo' => 0,
            'pontos_acumulados_total' => 0,
            'pontos_resgatados_total' => 0,
            'ativo' => true,
        ]);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => $user->id,
            'canal_pedido' => CanalPedido::App,
            'status' => OrderStatus::Pronto,
            'subtotal' => 50.00,
            'total' => 50.00,
        ]);

        Pagamento::factory()->create([
            'pedido_id' => $pedido->id,
            'status' => PagamentoStatus::Aprovado,
            'forma_pagamento' => TipoPagamento::MOCK->value,
            'valor' => $pedido->total,
        ]);

        $payload = ['status' => OrderStatus::Entregue];

        $response = $this->patchJson('api/pedidos/' . $pedido->id . '/status', $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseHas('fidelizacoes', [
            'user_id' => $user->id,
            'pontos_saldo' => 5,
        ]);
        $this->assertDatabaseHas('transacoes_fidelizacao', [
            'fidelizacao_id' => $fidelizacao->id,
            'pedido_id' => $pedido->id,
            'tipo' => TipoTransacaoFidelizacao::Acumulo,
            'pontos' => 5,
        ]);
    }

    public function test_cliente_can_view_pontos_saldo()
    {
        $user = $this->authenticate();
        Fidelizacao::factory()->create([
            'user_id' => $user->id,
            'pontos_saldo' => 50,
            'ativo' => true,
        ]);

        $response = $this->getJson('api/fidelizacoes/saldo');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonFragment([
            'saldo' => 50,
        ]);
    }

    public function test_cliente_can_redeem_points()
    {
        $user = $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio->produtos, $unidade);

        $fidelizacao = Fidelizacao::factory()->create([
            'user_id' => $user->id,
            'pontos_saldo' => 50,
            'pontos_resgatados_total' => 0,
            'pontos_acumulados_total' => 0,
            'ativo' => true,
        ]);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => $user->id,
            'canal_pedido' => CanalPedido::App,
            'subtotal' => 30.00,
            'desconto' => 0,
            'total' => 30.00,
        ]);

        $response = $this->postJson('api/pedidos/' . $pedido->id . '/fidelidade/resgate', [
            'pontos' => 20
        ]);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseHas('fidelizacoes', ['user_id' => $user->id, 'pontos_saldo' => 30,]);
        $this->assertDatabaseHas('pedidos', ['id' => $pedido->id, 'desconto' => 2.00, 'total' => 28.00,]);
        $this->assertDatabaseHas('transacoes_fidelizacao', ['fidelizacao_id' => $fidelizacao->id,
            'pedido_id' => $pedido->id,
            'tipo' => TipoTransacaoFidelizacao::Resgate,
            'pontos' => -20,
        ]);
    }

    public function test_cliente_cannot_redeem_more_points_than_balance()
    {
        $user = $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio->produtos, $unidade);

        $fidelizacao = Fidelizacao::factory()->create([
            'user_id' => $user->id,
            'pontos_saldo' => 10,
            'pontos_resgatados_total' => 0,
            'pontos_acumulados_total' => 0,
            'ativo' => true,
        ]);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => $user->id,
            'canal_pedido' => CanalPedido::App,
            'subtotal' => 30.00,
            'desconto' => 0,
            'total' => 30.00,
        ]);

        $response = $this->postJson('api/pedidos/' . $pedido->id . '/fidelidade/resgate', [
            'pontos' => 20
        ]);
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseHas('fidelizacoes', [
            'user_id' => $user->id,
            'pontos_saldo' => 10,
        ]);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'desconto' => 0,
            'total' => 30.00,
        ]);
        $this->assertDatabaseMissing('transacoes_fidelizacao', [
            'fidelizacao_id' => $fidelizacao->id,
            'tipo' => TipoTransacaoFidelizacao::Resgate,
        ]);
    }

    public function test_fidelizacao_requires_lgpd_consent()
    {
        $user = User::factory()->create([
            'consentimento_lgpd' => false
        ]);

        $atendente = $this->authenticate(UserRole::ATENDENTE);
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio->produtos, $unidade);
        $this->vincularUsuarioUnidade($atendente,$unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => $user->id,
            'canal_pedido' => CanalPedido::App,
            'status' => OrderStatus::Pronto,
            'subtotal' => 30.00,
            'desconto' => 0,
            'total' => 30.00,
        ]);

        $payload = ['status' => OrderStatus::Entregue];

        $response = $this->patchJson('api/pedidos/' . $pedido->id . '/status', $payload);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseMissing('fidelizacoes', ['user_id' => $user->id,]);
        $this->assertDatabaseMissing('transacoes_fidelizacao', ['pedido_id' => $pedido->id,]);
    }
}

//test_cliente_accumulates_points_after_pedido_entregue x
//test_cliente_can_view_pontos_saldo x
//test_cliente_can_redeem_points x
//test_cliente_cannot_redeem_more_points_than_balance x
//test_fidelizacao_requires_lgpd_consent x
