<?php

namespace Database\Factories;

use App\Enums\TipoTransacaoFidelizacao;
use App\Models\Fidelizacao;
use App\Models\Pedido;
use App\Models\TransacaoFidelizacao;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransacaoFidelizacao>
 */
class TransacaoFidelizacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipo = fake()->randomElement(TipoTransacaoFidelizacao::cases());

        return [
            'fidelizacao_id' => Fidelizacao::factory(),
            'pedido_id' => Pedido::factory(),
            'tipo' => $tipo,
            'pontos' => $tipo === TipoTransacaoFidelizacao::Acumulo
                ? fake()->numberBetween(10, 200)
                : fake()->numberBetween(50, 500),
            'descricao' => $tipo === TipoTransacaoFidelizacao::Acumulo
                ? 'Pontos recebidos por compra'
                : 'Resgate de recompensa no checkout',
        ];
    }

    public function acumulo(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => TipoTransacaoFidelizacao::Acumulo,
            'descricao' => 'Bônus de acúmulo de pontos',
        ]);
    }

    public function resgate(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => TipoTransacaoFidelizacao::Resgate,
            'descricao' => 'Pontos resgatados',
        ]);
    }
}
