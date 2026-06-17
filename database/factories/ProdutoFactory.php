<?php

namespace Database\Factories;

use App\Models\Categoria;
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
        $produtos = [
            'Cuscuz com Queijo Coalho',
            'Cuscuz com Carne de Sol',
            'Cuscuz Nordestino',
            'Cuscuz com Frango Desfiado',
            'Cuscuz Especial da Casa',
            'Tapioca de Queijo',
            'Tapioca de Carne Seca',
            'Suco de Cajá',
            'Suco de Acerola',
            'Café com Leite',
        ];

        return [
            'categoria_id' => Categoria::factory(),
            'nome' => fake()->randomElement($produtos),
            'descricao' => fake()->sentence(),
            'preco_base' => fake()->randomFloat(2, 5, 50),
            'disponivel_periodo_junino' => fake()->boolean(30),
            'ativo' => true,
        ];
    }

    public function inativo(): static
    {
        return $this->state(fn() => [
            'ativo' => false,
        ]);
    }

    public function junino(): static
    {
        return $this->state(fn() => [
            'disponivel_periodo_junino' => true,
        ]);
    }
}
