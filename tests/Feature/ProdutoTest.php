<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class ProdutoTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_user_can_create_a_product(): void
    {
        $user = User::factory()->create();

        $payload = [
            'nome' => 'Notebook Gamer',
            'descricao' => 'Processador i7, 16GB RAM, SSD 512GB',
            'preco' => 5999.90,
        ];

        $response = $this->actingAs($user)
            ->post('/api/produtos', $payload);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);

        $this->assertDatabaseHas('produtos', [
            'nome' => 'Notebook Gamer',
            'preco' => 5999.90,
        ]);
    }

    public function test_user_can_update_a_product(): void
    {
        $user = User::factory()->create();
        $produto = Produto::factory()->create([
            'nome' => 'Notebook Antigo',
            'preco' => 1000.00,
        ]);

        $payload = [
            'nome' => 'Notebook Gamer Atualizado',
            'descricao' => 'Nova descrição aqui',
            'preco' => 7500.00,
        ];

        $response = $this->actingAs($user)
            ->putJson('/api/produtos/' . $produto->id, $payload);

        $response->assertStatus(ResponseAlias::HTTP_OK);

        $this->assertDatabaseHas('produtos', [
            'id' => $produto->id,
            'nome' => 'Notebook Gamer Atualizado',
            'preco' => 7500.00,
        ]);
    }
}
