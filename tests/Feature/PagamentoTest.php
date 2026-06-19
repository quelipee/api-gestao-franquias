<?php

namespace Tests\Feature;

use App\Enums\CanalPedido;
use App\Enums\UserRole;
use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
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
        $produto = Produto::factory()->create();
        $unidade->produtos()->attach($produto->id, [
            'disponivel' => $disponivel,
        ]);
        $unidade->load('produtos');

        return $unidade->toArray();
    }

    public function test_cliente_can_create_pedido()
    {
        $user = $this->authenticate(); // cria o usuario autenticado
        $unidade = $this->createUnidade()->first(); // cria a unidade
        $this->unidadeAttachProduto($unidade); // vincula o produto com a unidade

        Estoque::factory()->create([ // cria o estoque vinculado com a unidade e o produto
            'unidade_id' => $unidade->id,
            'produto_id' => $unidade->produtos->first()->id,
            'quantidade' => 10,
            'quantidade_minima' => 1,
        ]);

        $payload = [
            'unidade_id' => $unidade->id,
            'user_id' => $user->id,
            'canal_pedido' => CanalPedido::App,
            'observação' => 'capricha pra mim pf'
        ];

    }
}

//test_cliente_can_create_pedido
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
