<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome'      => $this->faker->words(3, true), // Gera um nome com 3 palavras
            'descricao' => $this->faker->sentence(),      // Gera uma frase de descrição
            'preco'     => $this->faker->randomFloat(2, 10, 1000), // Preço entre 10 e 1000 com 2 casas decimais
        ];
    }
}
