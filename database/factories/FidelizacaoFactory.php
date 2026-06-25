<?php

namespace Database\Factories;

use App\Models\Fidelizacao;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fidelizacao>
 */
class FidelizacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pontosSaldo = fake()->numberBetween(0, 1000);
        $pontosResgatados = fake()->numberBetween(0, 500);
        $pontosTotal = $pontosSaldo + $pontosResgatados;

        return [
            'user_id' => User::factory(),
            'pontos_saldo' => $pontosSaldo,
            'pontos_acumulados_total' => $pontosTotal,
            'pontos_resgatados_total' => $pontosResgatados,
            'ativo' => fake()->boolean(90),
        ];
    }

    public function inativo(): static
    {
        return $this->state(fn(array $attributes) => [
            'ativo' => false,
        ]);
    }

    public function novo(): static
    {
        return $this->state(fn(array $attributes) => [
            'pontos_saldo' => 0,
            'pontos_acumulados_total' => 0,
            'pontos_resgatados_total' => 0,
            'ativo' => true,
        ]);
    }
}
