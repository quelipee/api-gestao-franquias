<?php

namespace Tests\Feature;

use App\Enums\TipoMovimentacaoEstoque;
use App\Enums\UserRole;
use App\Models\CardapioUnidade;
use App\Models\Categoria;
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

class EstoqueTest extends TestCase
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

    protected function createCategoria(int $qtd = 5): Collection
    {
        return Categoria::factory()->count($qtd)->create();
    }

    protected function unidadeAttachProduto(Unidade $unidade, bool $disponivel = true)
    {
        $produto = Produto::factory()->create();

        CardapioUnidade::create([
            'produto_id' => $produto->id,
            'unidade_id' => $unidade->id,
            'disponivel' => $disponivel,
        ]);
        $unidade->load('produtos');

        return $unidade->toArray();
    }

    protected function vincularUsuarioUnidade(User $user, Unidade $unidade): void
    {
        $unidade->users()->attach($user->id);
    }

    public function test_gerente_can_add_estoque_entry()
    {
        $unidade = $this->createUnidade()->get(1);
        $this->unidadeAttachProduto($unidade);
        $produto = $unidade->produtos->first();

        $gerente = $this->authenticate(UserRole::GERENTE);
        $this->vincularUsuarioUnidade($gerente, $unidade);

        $payload = [
            'unidade_id' => $unidade->id,
            'produto_id' => $produto->id,
            'quantidade' => 5,
            'quantidade_minima' => 1
        ];

        $response = $this->postJson('/api/estoque', $payload);
        $response->assertStatus(ResponseAlias::HTTP_CREATED);
    }

    public function test_gerente_can_subtract_estoque()
    {
        $unidade = $this->createUnidade()->get(1);
        $this->unidadeAttachProduto($unidade);
        $produto = $unidade->produtos->first();

        $gerente = $this->authenticate(UserRole::GERENTE);
        $this->vincularUsuarioUnidade($gerente, $unidade);

        $estoque = Estoque::create([
            'unidade_id' => $unidade->id,
            'produto_id' => $produto->id,
            'quantidade' => 10,
            'quantidade_minima' => 1,
        ]);

        $payload = [
            'unidade_id' => $unidade->id,
            'estoque_id' => $estoque->id,
            'tipo' => TipoMovimentacaoEstoque::SAIDA->value,
            'quantidade' => 5,
            'motivo' => 'saiu 5 pedidos'
        ];

        $response = $this->postJson('/api/estoque/movimentacao', $payload);
        $response->assertStatus(ResponseAlias::HTTP_CREATED);
        $this->assertDatabaseHas('estoques', [
            'id' => $estoque->id,
            'quantidade' => 5,
        ]);
        $this->assertDatabaseHas('movimentacoes_estoque', [
            'estoque_id' => $estoque->id,
            'tipo' => TipoMovimentacaoEstoque::SAIDA->value,
            'quantidade' => 5,
        ]);
        $this->assertDatabaseMissing('estoques', [
            'id' => $estoque->id,
            'quantidade' => -1,
        ]);
    }

    public function test_gerente_can_view_estoque_by_unidade()
    {
        $unidade = $this->createUnidade()->get(1);
        $this->unidadeAttachProduto($unidade);

        $gerente = $this->authenticate(UserRole::GERENTE);
        $this->vincularUsuarioUnidade($gerente, $unidade);

        Estoque::factory()->count(5)->create();
        Estoque::factory()->count(5)->create([
            'unidade_id' => $unidade->id
        ]);

        $response = $this->getjson('/api/estoque/' . $unidade->id);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(5, 'data');
        foreach ($response->json('data') as $estoque) {
            $this->assertEquals($unidade->id, $estoque['unidade_id']);
        }
    }

    public function test_estoque_cannot_go_below_zero()
    {
        $unidade = $this->createUnidade()->get(0);
        $this->unidadeAttachProduto($unidade);

        $gerente = $this->authenticate(UserRole::GERENTE);
        $this->vincularUsuarioUnidade($gerente, $unidade);

        $estoque = Estoque::factory()->create([
            'unidade_id' => $unidade->id,
            'produto_id' => $unidade->produtos->first()->id,
            'quantidade' => 5,
            'quantidade_minima' => 1
        ]);

        $payload = [
            'unidade_id' => $unidade->id,
            'estoque_id' => $estoque->id,
            'tipo' => TipoMovimentacaoEstoque::SAIDA->value,
            'quantidade' => 6,
            'motivo' => 'saiu 5 pedidos'
        ];

        $response = $this->postJson('/api/estoque/movimentacao', $payload);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['message' => 'Estoque insuficiente.']);
        $this->assertDatabaseHas('estoques', [
            'id' => $estoque->id,
            'quantidade' => 5,
        ]);
    }
}

//test_gerente_can_add_estoque_entry x
//test_gerente_can_subtract_estoque x
//test_gerente_can_view_estoque_by_unidade x
//test_estoque_cannot_go_below_zero x
