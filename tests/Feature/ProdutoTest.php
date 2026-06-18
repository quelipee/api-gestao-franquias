<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Categoria;
use App\Models\Produto;
use App\Models\Unidade;
use App\Models\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class ProdutoTest extends TestCase
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

    protected function unidadeAttachProduto(bool $disponivel = true)
    {
        $unidade = $this->createUnidade()->first();
        $produto = Produto::factory()->create();
        $unidade->produtos()->attach($produto->id, [
            'disponivel' => $disponivel,
        ]);
        $unidade->load('produtos');

        return $unidade->toArray();
    }

    public function test_admin_can_create_produto(): void
    {
        $this->authenticate(UserRole::ADMIN);
        $categoria = $this->createCategoria()->first();

        $payload = [
            'categoria_id' => $categoria->id,
            'nome' => 'Tapioca de Queijo Coalho',
            'descricao' => 'Tapioca recheada com queijo coalho grelhado',
            'preco_base' => 12.90,
            'disponivel_periodo_junino' => false,
            'ativo' => true,
        ];

        $response = $this->postJson('/api/produtos', $payload);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);

        $this->assertDatabaseHas('produtos', $payload);
    }

    public function test_admin_can_update_produto(): void
    {
        $this->authenticate(UserRole::ADMIN);
        $categoria = $this->createCategoria()->first();

        $produto = Produto::factory()->create([
            'categoria_id' => $categoria->id,
            'nome' => 'Tapioca de Carne de Sol',
            'descricao' => 'Tapioca recheada com carne de sol desfiada e manteiga de garrafa',
            'preco_base' => 16.90,
            'disponivel_periodo_junino' => false,
            'ativo' => true,
        ]);

        $payload = [
            'categoria_id' => $categoria->id,
            'nome' => 'Tapioca Especial de Carne de Sol',
            'descricao' => 'Tapioca recheada com carne de sol, queijo coalho e manteiga de garrafa',
            'preco_base' => 19.90,
            'disponivel_periodo_junino' => true,
            'ativo' => true,
        ];

        $response = $this->putJson('/api/produtos/' . $produto->id, $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);

        $response->assertJsonStructure([
            'message',
            'data' => [
                'id', 'categoria_id', 'nome',
                'descricao', 'preco_base', 'disponivel_periodo_junino',
                'ativo', 'created_at', 'updated_at', 'deleted_at',
            ],
        ]);

        $this->assertDatabaseHas('produtos', $payload);
    }

    public function test_admin_can_attach_produto_to_unidade()
    {
        $this->authenticate(UserRole::ADMIN);
        $unidade = $this->createUnidade()->first();
        $produto = Produto::factory()->create();

        $payload = [
            'produto_id' => $produto->id,
            'unidade_id' => $unidade->id,
            'disponivel' => true,
        ];

        $response = $this->postJson('/api/unidades/' . $unidade->id . '/produtos', $payload);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);
        $this->assertDatabaseHas('cardapio_unidade', $payload);
    }

    public function test_admin_can_detach_produto_from_unidade()
    {
        $this->authenticate(UserRole::ADMIN);
        $unidade = $this->unidadeAttachProduto();

        $response = $this->deleteJson('/api/unidades/' . $unidade['id'] .
            '/produtos/' . $unidade['produtos'][0]['id']);

        $response->assertStatus(ResponseAlias::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('cardapio_unidade', $unidade);
    }

    public function test_user_can_list_produtos_by_unidade()
    {
        $this->authenticate();
        $unidade = $this->unidadeAttachProduto();

        $response = $this->getjson('/api/unidades/' . $unidade['id'] . '/produtos');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonFragment([
            'id' => $unidade['produtos'][0]['id'],
            'nome' => $unidade['produtos'][0]['nome'],
        ]);
        $response->assertJsonCount(1, 'data.produtos');
    }
    public function test_user_cannot_see_produto_unavailable_in_unidade()
    {
        $this->authenticate();
        $unidade = $this->unidadeAttachProduto(false);

        $response = $this->getjson('/api/unidades/' . $unidade['id'] . '/produtos');

        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonMissing([
            'pivot' => [
                'disponivel' => false
            ]
        ]);
    }
}


//test_admin_can_create_produto x
//test_admin_can_update_produto x
//test_admin_can_attach_produto_to_unidade x
//test_admin_can_detach_produto_from_unidade X
//test_user_can_list_produtos_by_unidade x
//test_user_cannot_see_produto_unavailable_in_unidade x
