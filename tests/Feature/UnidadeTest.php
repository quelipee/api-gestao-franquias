<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class UnidadeTest extends TestCase
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

    public function test_admin_can_create_unidade()
    {
        $this->authenticate(UserRole::ADMIN);

        $payload = [
            "nome" => "Unidade Central São Paulo",
            "cnpj" => "12345678000199",
            "cidade" => "São Paulo",
            "estado" => "SP",
            "endereco" => "Avenida Paulista, 1000 - Bela Vista",
            "telefone" => "11999998888",
            "tipo" => "COMPLETA",
            "ativo" => true,
            "horario_inicio" => "08:00:00",
            "horario_fim" => "18:00:00"
        ];

        $response = $this->postJson('/api/unidades', $payload);
        $response->assertStatus(ResponseAlias::HTTP_CREATED);
        $this->assertDatabaseHas('unidades', [
            'cnpj' => $payload['cnpj'],
            'nome' => $payload['nome'],
        ]);
    }

    public function test_user_can_list_unidades()
    {
        $this->createUnidade(10);
        $this->authenticate();

        $response = $this->getJson('/api/unidades');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'current_page',
                'data',
                'per_page',
                'total',
            ]
        ]);
    }

    public function test_user_can_show_unidade()
    {
        $unidade = $this->createUnidade(1);
        $this->authenticate();

        $response = $this->getJson('/api/unidades/' . $unidade[0]->id);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'nome', 'cidade', 'estado', 'endereco',
                'telefone', 'tipo', 'horario_inicio', 'horario_fim'
            ]
        ])->assertJsonMissing([
            'cnpj', 'ativo', 'created_at', 'deleted_at'
        ]);
    }

    public function test_admin_can_update_unidade()
    {
        $this->authenticate(UserRole::ADMIN);
        $unidade = $this->createUnidade(1);

        $payload = [
            'nome' => 'Unidade Mercado da Tapioca',
            'cidade' => 'Sorocaba',
            'estado' => 'SP',
        ];

        $response = $this->putJson('/api/unidades/' . $unidade[0]->id, $payload);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertDatabaseHas('unidades', [
            'nome' => $payload['nome'],
            'cidade' => $payload['cidade'],
            'estado' => $payload['estado'],
        ]);
    }

    public function test_admin_can_delete_unidade()
    {
        $this->authenticate(UserRole::ADMIN);
        $unidade = $this->createUnidade(2)->first();

        $response = $this->deleteJson('/api/unidades/' . $unidade->id);
        $response->assertStatus(ResponseAlias::HTTP_NO_CONTENT);
        $this->assertsoftDeleted('unidades', [
            'id' => $unidade->id
        ]);
    }
    public function test_non_admin_cannot_create_unidade()
    {
        $this->authenticate();
        $payload = [
            'nome' => 'Nova Unidade Invasora',
            'cidade' => 'Sorocaba',
            'estado' => 'SP',
            'endereco' => 'Rua das Flores, 123'
        ];

        $response = $this->postJson('/api/unidades', $payload);
        $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing('unidades', [
            'nome' => $payload['nome'],
        ]);
    }
}

//test_user_can_list_unidades x
//test_user_can_show_unidade x
//test_admin_can_create_unidade x
//test_admin_can_update_unidade x
//test_admin_can_delete_unidade x
//test_non_admin_cannot_create_unidade x
