<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'type' => 'main',
            'name' => $this->faker->randomElement([
                'Almacén Principal',
                'Almacén de Materia Prima',
                'Almacén de Producto Terminado',
                'Almacén de Insumos',
            ]),
            'active' => $this->faker->boolean(90),
            'created_by' => null,
        ];
    }
}
