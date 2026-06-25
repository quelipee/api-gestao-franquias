<?php

namespace Tests\Feature;

use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Enums\PagamentoStatus;
use App\Enums\TipoPagamento;
use App\Enums\UserRole;
use App\Models\CardapioUnidade;
use App\Models\Estoque;
use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class PagamentoTest extends TestCase
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

    public function test_pagamento_mock_aprovado_updates_pedido_status(): void
    {
        $user = $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio->produtos, $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => $user->id,
            'canal_pedido' => CanalPedido::App,
            'subtotal' => 25.80,
            'total' => 25.80,
        ]);

        Pagamento::factory()->create([
            'pedido_id' => $pedido->id,
            'forma_pagamento' => TipoPagamento::MOCK->value,
            'valor' => $pedido->total,
        ]);

        $payload = [
            'forma_pagamento' => TipoPagamento::MOCK,
            'simular_resultado' => PagamentoStatus::Aprovado,
        ];

        $response = $this->postJson('/api/pedidos/' . $pedido->id . '/pagamento', $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonFragment(['status' => PagamentoStatus::Aprovado]);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => OrderStatus::Pago,
        ]);
    }

    public function test_pagamento_mock_recusado_keeps_pedido_status(): void
    {
        $user = $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio->produtos, $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => $user->id,
            'canal_pedido' => CanalPedido::App,
            'subtotal' => 25.80,
            'total' => 25.80,
        ]);

        Pagamento::factory()->create([
            'pedido_id' => $pedido->id,
            'forma_pagamento' => TipoPagamento::MOCK->value,
            'valor' => $pedido->total,
        ]);

        $payload = [
            'forma_pagamento' => TipoPagamento::MOCK,
            'simular_resultado' => PagamentoStatus::Negado,
        ];

        $response = $this->postJson('/api/pedidos/' . $pedido->id . '/pagamento', $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => OrderStatus::AguardandoPagamento->value,
        ]);
    }

    public function test_cannot_pay_already_paid_pedido(): void
    {
        $user = $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio->produtos, $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => $user->id,
            'status' => OrderStatus::Pago,
            'canal_pedido' => CanalPedido::App,
            'subtotal' => 25.80,
            'total' => 25.80,
        ]);

        Pagamento::factory()->create([
            'pedido_id' => $pedido->id,
            'status' => PagamentoStatus::Aprovado,
            'forma_pagamento' => TipoPagamento::MOCK->value,
            'valor' => $pedido->total,
        ]);

        $payload = [
            'forma_pagamento' => TipoPagamento::MOCK,
            'simular_resultado' => PagamentoStatus::Aprovado,
        ];

        $response = $this->postJson('/api/pedidos/' . $pedido->id . '/pagamento', $payload);

        $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => OrderStatus::Pago,
        ]);
    }
}

//test_pagamento_mock_aprovado_updates_pedido_status x
//test_pagamento_mock_recusado_keeps_pedido_status x
//test_pagamento_registers_gateway_payload
//test_cannot_pay_already_paid_pedido
