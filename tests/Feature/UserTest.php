<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Unidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class UserTest extends TestCase
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

    public function test_user_can_register(): void
    {
        $payload = [
            'name' => 'felipe1',
            'email' => 'fe1@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'cpf' => '12345678911',
            'role' => UserRole::CLIENTE->value,
            'consentimento_lgpd' => true,
        ];
        $response = $this->postJson('/api/register', $payload);
        $response->assertStatus(ResponseAlias::HTTP_CREATED);
    }

    public function test_user_cannot_register_with_invalid_data()
    {
        $payload = [
            'name' => '',
            'email' => 'email-invalido',
            'password' => '123',
            'password_confirmation' => '98765432',
            'cpf' => '1234',
            'role' => 'GERENTE_SUPER_VIP',
            'ativo' => 'texto-em-vez-de-bool',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'name', 'email', 'password', 'cpf', 'role', 'ativo'
        ]);
    }

    public function test_user_can_authenticate(): void
    {
        User::factory()->create([
            'name' => 'felipe1',
            'email' => 'fe@gmail.com',
            'password' => '12345678',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'fe@gmail.com',
            'password' => '12345678',
        ]);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonStructure(['token', 'user']);
    }

    public function test_user_cannot_authenticate_with_wrong_password()
    {
        User::factory()->create([
            'name' => 'felipe1',
            'email' => 'fe@gmail.com',
            'password' => '12345678',
        ]);

        $payload = [
            'email' => 'fe@gmail.com',
            'password' => 'senhaerrada',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(ResponseAlias::HTTP_UNAUTHORIZED);

        $response->assertJson([
            'message' => 'Senha incorreta.'
        ]);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create([
            'name' => 'felipe1',
            'email' => 'fe@gmail.com',
            'password' => '12345678',
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/logout');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJson(['message' => 'Logout realizado com sucesso!']);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(ResponseAlias::HTTP_UNAUTHORIZED);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function test_user_without_permission_cannot_access_admin_routes()
    {
        $payload = [
            'nome' => 'felipe1',
            'descricao' => 'dsadasda',
            'preco' => 12.21
        ];
        $user = User::factory()->create([
            'role' => UserRole::CLIENTE->value,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/produtos', $payload);
        $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
    }
}
