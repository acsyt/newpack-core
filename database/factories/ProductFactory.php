<?php

namespace Database\Factories;

use App\Enums\ProductType;
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
            'product_type_id'           => \App\Models\ProductType::inRandomOrder()->first()?->id ?? 1,
            'measure_unit_id'           => $this->faker->numberBetween(1, 5),
            'average_cost'              => $this->faker->randomFloat(4, 1, 1000),
            'last_purchase_price'       => $this->faker->randomFloat(4, 1, 1000),
            'current_stock'             => $this->faker->randomFloat(4, 0, 1000),
            'min_stock'                 => $this->faker->randomFloat(4, 10, 50),
            'max_stock'                 => $this->faker->randomFloat(4, 100, 500),
            'is_active'                 => true,
            'is_sellable'               => $this->faker->boolean(50),
            'is_purchasable'            => $this->faker->boolean(50),
        ];
    }

    public function rawMaterial(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type_id'   => \App\Models\ProductType::where('code', ProductType::RAW_MATERIAL->value)->first()?->id,
            'is_purchasable'    => true,
            'is_sellable'       => false,
        ]);
    }

    public function compound(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type_id'   => \App\Models\ProductType::where('code', ProductType::COMPOUND->value)->first()?->id,
            'is_purchasable'    => false,
            'is_sellable'       => true,
            'average_cost'      => 0, // El costo depende de la receta
        ]);
    }
}
