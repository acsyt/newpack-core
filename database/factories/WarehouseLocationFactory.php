<?php

namespace Database\Factories;

use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WarehouseLocationFactory extends Factory
{
    protected $model = WarehouseLocation::class;

    public function definition(): array
    {
        return [
            'unique_id'         => Str::uuid(),
            'warehouse_id' => Warehouse::factory(),
            'aisle' => 'A-' . $this->faker->numberBetween(1, 20),
            'shelf' => 'S-' . $this->faker->numberBetween(1, 10),
            'section' => 'L-' . $this->faker->numberBetween(1, 5),
        ];
    }
}
