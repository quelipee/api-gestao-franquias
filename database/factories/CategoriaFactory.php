<?php

namespace Database\Factories;

use App\Models\Categoria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Categoria>
 */
class CategoriaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categorias = [
            [
                "nome" => "Tapiocas",
                "descricao" => "Tapiocas recheadas com ingredientes regionais"
            ],
            [
                "nome" => "Bebidas",
                "descricao" => "Sucos naturais e bebidas regionais"
            ],
            [
                "nome" => "Doces e Bolos",
                "descricao" => "Bolos, cocadas e doces típicos nordestinos"
            ]
        ];

        return fake()->randomElement($categorias);
    }
}
