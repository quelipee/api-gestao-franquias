<?php

namespace Tests\Feature;

use App\Enums\AuditoriaAcao;
use App\Enums\AuditoriaEntidade;
use App\Enums\CanalPedido;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\CardapioUnidade;
use App\Models\Estoque;
use App\Models\LogAuditoria;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class LogsAuditoriaTest extends TestCase
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

    public function test_log_is_created_when_pedido_is_created()
    {
        $user = $this->authenticate();
        $unidade = $this->createUnidade()->first();
        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio->produtos, $unidade);

        $payload = [
            'unidade_id' => $unidade->id,
            'canal_pedido' => CanalPedido::App,
            'itens' => [
                ['produto_id' => $cardapio->produtos()->first()->id, 'quantidade' => 2],
            ],
        ];

        $response = $this->postJson('/api/pedido', $payload);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);

        $this->assertDatabaseHas('logs_auditoria', [
            'user_id' => $user->id,
            'acao' => AuditoriaAcao::PedidoCriado,
            'entidade' => AuditoriaEntidade::Pedido,
            'entidade_id' => $response->json('pedido.id'),
        ]);
    }

    public function test_log_is_created_when_status_is_updated()
    {
        $user = $this->authenticate(UserRole::COZINHA);
        $unidade = $this->createUnidade()->first();
        $this->vincularUsuarioUnidade($user, $unidade);

        $cardapio = $this->unidadeAttachProduto($unidade);
        $this->createEstoque($cardapio['produtos'], $unidade);

        $pedido = Pedido::factory()->create([
            'unidade_id' => $unidade->id,
            'user_id' => User::factory()->create(),
            'canal_pedido' => CanalPedido::Web,
            'status' => OrderStatus::Pago,
            'subtotal' => 100,
            'total' => 100,
        ]);

        $payload = ['status' => OrderStatus::EmPreparo];

        $response = $this->putJson('/api/pedidos/' . $pedido->id, $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);

        $this->assertDatabaseHas('logs_auditoria', [
            'user_id' => $user->id,
            'acao' => AuditoriaAcao::StatusAtualizado,
            'entidade' => AuditoriaEntidade::Pedido,
            'entidade_id' => $pedido->id,
        ]);

        $log = LogAuditoria::where('entidade_id', $pedido->id)
            ->where('acao', AuditoriaAcao::StatusAtualizado)
            ->first();

        $this->assertEquals(OrderStatus::Pago->value, $log->dados_anteriores['status']);
        $this->assertEquals(OrderStatus::EmPreparo->value, $log->dados_novos['status']);
    }
}

//test_log_is_created_when_pedido_is_created x
//test_log_is_created_when_status_is_updated x
//test_log_is_created_when_pedido_is_cancelled
