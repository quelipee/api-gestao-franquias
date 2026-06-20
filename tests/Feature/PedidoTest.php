<?php

namespace Tests\Feature;

use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\CardapioUnidade;
use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
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

        $response = $this->postJson('/api/pedido', $payload);

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
}

//test_cliente_can_create_pedido x
//test_pedido_requires_canal_pedido
//test_pedido_with_invalid_canal_pedido_is_rejected
//test_pedido_is_rejected_when_produto_unavailable_in_unidade
//test_pedido_is_rejected_when_estoque_insuficiente
//test_pedido_can_be_filtered_by_canal
//test_pedido_can_be_filtered_by_status
//test_cozinha_can_update_status_to_em_preparo
//test_cozinha_can_update_status_to_pronto
//test_atendente_can_update_status_to_entregue
//test_gerente_can_cancel_pedido
//test_cliente_cannot_cancel_pedido_of_another_cliente
//test_pedido_status_cannot_go_backwards
