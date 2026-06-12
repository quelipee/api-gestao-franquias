<?php

namespace Database\Factories;

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
        $nomes = ['Matriz Itapeva', 'Filial Centro', 'Unidade Norte', 'Centro de Distribuição'];

        return [
            'nome'   => $this->faker->randomElement($nomes),
            'cidade' => $this->faker->city(),
            'estado' => $this->faker->stateAbbr(),
            'ativo'  => $this->faker->boolean(90),
        ];
    }
}
