<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'                      => $this->faker->words(3, true),
            'sku'                       => strtoupper($this->faker->unique()->bothify('PROD-####')),
            'type'                      => $this->faker->randomElement(['materia_prima', 'compuesto', 'insumo', 'servicio']),
            'unit_of_measure'           => $this->faker->randomElement(['kg', 'lt', 'pza', 'm']),
            'average_cost'              => $this->faker->randomFloat(4, 1, 1000),
            'last_purchase_price'       => $this->faker->randomFloat(4, 1, 1000),
            'current_stock'             => $this->faker->randomFloat(4, 0, 1000),
            'min_stock'                 => $this->faker->randomFloat(4, 10, 50),
            'max_stock'                 => $this->faker->randomFloat(4, 100, 500),
            'track_batches'             => $this->faker->boolean(20),
            'is_active'                 => true,
            'is_sellable'               => $this->faker->boolean(50),
            'is_purchasable'            => $this->faker->boolean(50),
        ];
    }

    public function rawMaterial(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'              => 'materia_prima',
            'is_purchasable'    => true,
            'is_sellable'       => false,
            'track_batches'     => true, // MP crítica suele llevar lotes
        ]);
    }

    public function compound(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'              => 'compuesto',
            'is_purchasable'    => false,
            'is_sellable'       => true,
            'average_cost'      => 0, // El costo depende de la receta
        ]);
    }
}
