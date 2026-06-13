<?php

namespace Database\Factories;

use App\Enums\TipoUnidade;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unidade>
 */
class UnidadeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nomes = ['Matriz Itapeva', 'Filial Centro', 'Unidade Norte', 'Centro de Distribuição', 'Filial Pocket'];

        $horarioInicio = $this->faker->time('H:i:s', '10:00:00');
        $horarioFim = $this->faker->time('H:i:s', '22:00:00');

        return [
            'nome' => $this->faker->randomElement($nomes) . ' ' . $this->faker->companySuffix(),
            'cnpj' => $this->faker->numerify('##############'),
            'cidade' => $this->faker->city(),
            'estado' => $this->faker->stateAbbr(),
            'endereco' => $this->faker->streetAddress() . ', ' . $this->faker->buildingNumber(),
            'telefone' => $this->faker->numerify('###########'),
            'tipo' => $this->faker->randomElement(TipoUnidade::cases()),
            'ativo' => $this->faker->boolean(90),
            'horario_inicio' => $this->faker->boolean(80) ? $horarioInicio : null,
            'horario_fim' => $this->faker->boolean(80) ? $horarioFim : null,
        ];
    }

    public function inativa(): static
    {
        return $this->state(fn(array $attributes) => [
            'ativo' => false,
        ]);
    }

    public function reduzida(): static
    {
        return $this->state(fn(array $attributes) => [
            'tipo' => TipoUnidade::REDUZIDA,
        ]);
    }
}
