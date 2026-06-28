<?php

namespace Tests\Feature;

use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\CardapioUnidade;
use App\Models\Estoque;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Unidade;
use App\Models\User;
use Closure;
use Couchbase\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class PedidoTest extends TestCase
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

    /**
     * @param $produtos
     * @param Unidade|null $unidade
     * @return void
     */
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

    public function test_cliente_can_create_pedido()
    {
        $this->authenticate(); // cria o usuario autenticado
        $unidade = $this->createUnidade()->first(); // cria a unidade
        $cardapio = $this->unidadeAttachProduto($unidade); // vincula o produto com a unidade

        foreach ($cardapio['produtos'] as $produto) {
            Estoque::factory()->create([ // cria o estoque vinculado com a unidade e o produto
                'unidade_id' => $unidade->id,
                'produto_id' => $produto['id'],
                'quantidade' => 10,
                'quantidade_minima' => 1,
            ]);
        }

        $payload = [
            'unidade_id' => $unidade->id,
            'canal_pedido' => CanalPedido::App,
            'itens' => [
                ['produto_id' => $cardapio->produtos[0]->id, 'quantidade' => 2],
                ['produto_id' => $cardapio->produtos[1]->id, 'quantidade' => 1],
            ],
        ];

        $response = $this->postJson('/api/pedidos', $payload);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);
        $response->assertJsonFragment(['status' => OrderStatus::AguardandoPagamento->value]);

        $this->assertDatabaseHas('itens_pedido', [
            'produto_id' => $cardapio->produtos[0]->id,
            'quantidade' => 2,
        ]);

        $this->assertDatabaseHas('itens_pedido', [
            'produto_id' => $cardapio->produtos[1]->id,
            'quantidade' => 1,
        ]);
    }

    public function test_pedido_requires_canal_pedido()
    {
        $this->authenticate(); // cria o usuario autenticado
        $unidade = $this->createUnidade()->first(); // cria a unidade
        $cardapio = $this->unidadeAttachProduto($unidade); // vincula o produto com a unidade

        $this->createEstoque($cardapio['produtos'], $unidade);

        $payload = [
            'unidade_id' => $unidade->id,
            'itens' => [
                ['produto_id' => $cardapio->produtos[0]->id, 'quantidade' => 2],
                ['produto_id' => $cardapio->produtos[1]->id, 'quantidade' => 1],
            ],
        ];

        $response = $this->postJson('/api/pedidos', $payload);
        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

        $response->assertJsonValidationErrors([
            'canal_pedido'
        ]);
    }

    public function test_pedido_with_invalid_canal_pedido_is_rejected()
    {
        $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);

        $this->createEstoque($cardapio['produtos'], $unidade);

        $payload = [
            'unidade_id' => $unidade->id,
            'canal_pedido' => 'canal_naoexiste',
            'itens' => [
                ['produto_id' => $cardapio->produtos[0]->id, 'quantidade' => 2],
                ['produto_id' => $cardapio->produtos[1]->id, 'quantidade' => 1],
            ],
        ];

        $response = $this->postJson('/api/pedidos', $payload);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'canal_pedido',
        ]);
    }

    public function test_pedido_is_rejected_when_produto_unavailable_in_unidade()
    {
        $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade, false);

        $this->createEstoque($cardapio['produtos'], $unidade);

        $payload = [
            'unidade_id' => $unidade->id,
            'canal_pedido' => CanalPedido::Web,
            'itens' => [
                ['produto_id' => $cardapio->produtos[0]->id, 'quantidade' => 2],
            ],
        ];

        $response = $this->postJson('/api/pedidos', $payload);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseMissing('pedidos', [
            'unidade_id' => $unidade->id
        ]);
    }

    public function test_pedido_is_rejected_when_estoque_insuficiente()
    {
        $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);

        $this->createEstoque($cardapio['produtos'], $unidade);

        $payload = [
            'unidade_id' => $unidade->id,
            'canal_pedido' => CanalPedido::Web,
            'itens' => [
                ['produto_id' => $cardapio->produtos[0]->id, 'quantidade' => 11],
            ],
        ];

        $response = $this->postJson('/api/pedidos', $payload);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseMissing('pedidos', [
            'unidade_id' => $unidade->id,
        ]);
        $this->assertDatabaseMissing('itens_pedido', [
            'produto_id' => $cardapio->produtos[0]->id,
        ]);
        $response->assertJsonFragment([
            'Estoque insuficiente.',
        ]);
    }

    public function test_pedido_can_be_filtered_by_canal()
    {
        $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => auth()->id(),
            'canal_pedido' => CanalPedido::Web,
            'subtotal' => 100,
            'total' => 100,
        ]);

        Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => auth()->id(),
            'canal_pedido' => CanalPedido::App,
            'subtotal' => 100,
            'total' => 100,
        ]);

        Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => auth()->id(),
            'canal_pedido' => CanalPedido::Totem,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $response = $this->getJson('/api/pedidos?canal_pedido=' . CanalPedido::Totem->value);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonFragment([
            'total' => 1,
            'canal_pedido' => CanalPedido::Totem->value
        ]);
    }

    public function test_pedido_can_be_filtered_by_status()
    {
        $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => auth()->id(),
            'canal_pedido' => CanalPedido::Web,
            'subtotal' => 100,
            'total' => 100,
        ]);

        Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => auth()->id(),
            'canal_pedido' => CanalPedido::Web,
            'subtotal' => 100,
            'total' => 100,
        ]);

        Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => auth()->id(),
            'canal_pedido' => CanalPedido::Totem,
            'status' => OrderStatus::EmPreparo->value,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $response = $this->getJson('/api/pedidos?status=' . OrderStatus::AguardandoPagamento->value);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertCount(2, $response->json('data.data'));
        foreach ($response->json('data.data') as $pedido) {
            $this->assertEquals(
                OrderStatus::AguardandoPagamento->value,
                $pedido['status']
            );
        }
    }

    public function test_cozinha_can_update_status_to_em_preparo()
    {
        $user = $this->authenticate(UserRole::COZINHA);
        $unidade = $this->createUnidade()->first();
        $this->vincularUsuarioUnidade($user, $unidade);

        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => auth()->id(),
            'canal_pedido' => CanalPedido::Web,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $payload = ['status' => OrderStatus::EmPreparo];

        $response = $this->patchJson('/api/pedidos/' . $pedido->id . '/status', $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => OrderStatus::EmPreparo->value,
        ]);
    }

    public function test_cozinha_can_update_status_to_pronto()
    {
        $user = $this->authenticate(UserRole::COZINHA);
        $unidade = $this->createUnidade()->first();
        $this->vincularUsuarioUnidade($user, $unidade);

        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => User::factory()->create()->id,
            'canal_pedido' => CanalPedido::Web,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $pedido2 = Pedido::factory()->create([
            'unidade_id' => 2,
            'user_id' => User::factory()->create()->id,
            'canal_pedido' => CanalPedido::Totem,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $payload = ['status' => OrderStatus::Pronto];

        $response = $this->patchJson('/api/pedidos/' . $pedido->id . '/status', $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => OrderStatus::Pronto->value,
        ]);
    }

    public function test_atendente_can_update_status_to_entregue()
    {
        $user = $this->authenticate(UserRole::ATENDENTE);
        $unidade = $this->createUnidade()->first();
        $this->vincularUsuarioUnidade($user, $unidade);

        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => User::factory()->create()->id,
            'canal_pedido' => CanalPedido::Web,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $payload = [
            'status' => OrderStatus::Entregue->value,
        ];

        $response = $this->patchJson('/api/pedidos/' . $pedido->id . '/status', $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => OrderStatus::Entregue->value,
        ]);

    }

    public function test_gerente_can_cancel_pedido()
    {
        $gerente = $this->authenticate(UserRole::GERENTE);
        $unidade = $this->createUnidade()->first();
        $this->vincularUsuarioUnidade($gerente, $unidade);

        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => auth()->id(),
            'canal_pedido' => CanalPedido::Web,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $payload = [
            'status' => OrderStatus::Cancelado,
            'motivo_cancelamento' => 'Cliente desistiu do pedido',
        ];

        $response = $this->patchJson('/api/pedidos/' . $pedido->id . '/status', $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => OrderStatus::Cancelado->value,
            'motivo_cancelamento' => 'Cliente desistiu do pedido',
        ]);

        $pedido->refresh();
        $this->assertEquals(
            OrderStatus::Cancelado,
            $pedido->status
        );
    }

    public function test_cliente_cannot_cancel_pedido_of_another_cliente()
    {
        $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => User::factory()->create(),
            'canal_pedido' => CanalPedido::Web,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $payload = [
            'status' => OrderStatus::Cancelado,
            'motivo_cancelamento' => 'demorou muito para fazer.',
        ];

        $response = $this->patchJson('/api/pedidos/' . $pedido->id . '/status', $payload);

        $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
    }

    public function test_pedido_status_cannot_go_backwards()
    {
        $cozinha = $this->authenticate(UserRole::GERENTE);
        $unidade = $this->createUnidade()->first();
        $this->vincularUsuarioUnidade($cozinha, $unidade);

        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => User::factory()->create()->id,
            'canal_pedido' => CanalPedido::Web,
            'status' => OrderStatus::Pronto->value,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $payload = [
            'status' => OrderStatus::EmPreparo,
        ];

        $response = $this->patchJson('/api/pedidos/' . $pedido->id . '/status', $payload);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'status' => OrderStatus::Pronto->value,
        ]);
    }
}

//test_cliente_can_create_pedido x
//test_pedido_requires_canal_pedido x
//test_pedido_with_invalid_canal_pedido_is_rejected x
//test_pedido_is_rejected_when_produto_unavailable_in_unidade x
//test_pedido_is_rejected_when_estoque_insuficiente x
//test_pedido_can_be_filtered_by_canal x
//test_pedido_can_be_filtered_by_status x
//test_cozinha_can_update_status_to_em_preparo x
//test_cozinha_can_update_status_to_pronto x
//test_atendente_can_update_status_to_entregue x
//test_gerente_can_cancel_pedido x
//test_cliente_cannot_cancel_pedido_of_another_cliente x
//test_pedido_status_cannot_go_backwards x
