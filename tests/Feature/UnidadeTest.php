<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class UnidadeTest extends TestCase
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

    public function test_admin_can_create_unidade()
    {
        $user = User::factory()->create([
            'role' => UserRole::GERENTE
        ]);

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

        $response = $this->actingAs($user,'sanctum')->postJson('/api/unidades', $payload);
        $response->assertStatus(ResponseAlias::HTTP_CREATED);
        $this->assertDatabaseHas('unidades', [
            'cnpj' => $payload['cnpj'],
            'nome' => $payload['nome'],
        ]);
    }
    public function test_user_can_list_unidades()
    {
        Unidade::factory()->count(10)->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user,'sanctum')->getJson('/api/unidades');
        $response->assertStatus(ResponseAlias::HTTP_OK);
    }
}

//test_user_can_list_unidades
//test_user_can_show_unidade
//test_admin_can_create_unidade x
//test_admin_can_update_unidade
//test_admin_can_delete_unidade
//test_non_admin_cannot_create_unidade
