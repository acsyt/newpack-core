<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $mainWarehouse = Warehouse::create([
            'type' => 'main',
            'name' => 'Almacén Principal',
            'active' => true,
        ]);

        $rawMaterialWarehouse = Warehouse::create([
            'type' => 'main',
            'name' => 'Almacén de Materia Prima',
            'active' => true,
        ]);

        $finishedGoodsWarehouse = Warehouse::create([
            'type' => 'main',
            'name' => 'Almacén de Producto Terminado',
            'active' => true,
        ]);

        $this->createLocationsForWarehouse($mainWarehouse, 5, 8, 4);
        $this->createLocationsForWarehouse($rawMaterialWarehouse, 3, 6, 3);
        $this->createLocationsForWarehouse($finishedGoodsWarehouse, 4, 7, 3);

        $this->command->info('✅ Created 3 warehouses with organized locations!');
    }

    private function createLocationsForWarehouse(Warehouse $warehouse, int $aisles, int $shelves, int $sections): void
    {
        for ($a = 1; $a <= $aisles; $a++) {
            for ($s = 1; $s <= $shelves; $s++) {
                for ($l = 1; $l <= $sections; $l++) {
                    WarehouseLocation::create([
                        'warehouse_id' => $warehouse->id,
                        'aisle' => 'A-' . str_pad($a, 2, '0', STR_PAD_LEFT),
                        'shelf' => 'S-' . str_pad($s, 2, '0', STR_PAD_LEFT),
                        'section' => 'L-' . str_pad($l, 2, '0', STR_PAD_LEFT),
                    ]);
                }
            }
        }
    }
}
