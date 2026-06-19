<?php

namespace Database\Factories;

use App\Models\Estoque;
use App\Models\Produto;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Estoque>
 */
class EstoqueFactory extends Factory
{
    protected $model = Estoque::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unidade_id' => Unidade::factory()->create()->id,
            'produto_id' => Produto::factory()->create()->id,
            'quantidade' => $this->faker->numberBetween(0, 1000),
            'quantidade_minima' => $this->faker->numberBetween(0, 50),
        ];
    }
}
