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
            'cpf' => '12345678911',
            'role' => UserRole::Cliente->value,
            'consentimento_lgpd' => true,
        ];
        $response = $this->post('/api/register', $payload);
        $response->assertStatus(ResponseAlias::HTTP_CREATED);
    }

    public function test_user_can_authenticate(): void
    {
        User::factory()->create([
            'email' => 'fe@gmail.com',
            'password' => '12345678',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'fe@gmail.com',
            'password' => '12345678',
        ]);
        $response->assertStatus(ResponseAlias::HTTP_OK);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $this->assertCount(0, $user->tokens); // verifica se o token foi deletado
    }

    public function test_user_can_list_unidades(): void
    {
        Unidade::factory()->count(10)->create();

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/unidades');
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonCount(10);
    }

    public function test_user_can_show_unidade(): void
    {
        $unidade = Unidade::factory()->count(10)->create()->first();

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/unidades/' . $unidade->id);
        $response->assertStatus(ResponseAlias::HTTP_OK);
        $response->assertJsonFragment(['nome' => $unidade->nome]);
    }
}
