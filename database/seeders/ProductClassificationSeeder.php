<?php

namespace Database\Seeders;

use App\Models\ProductClass;
use App\Models\ProductSubclass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductClassificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['id' => 1, 'name' => 'RESINAS'],
            ['id' => 2, 'name' => 'ADITIVOS'],
            ['id' => 3, 'name' => 'PIGMENTOS'],
            ['id' => 4, 'name' => 'BOBINAS'],
            ['id' => 5, 'name' => 'BOLSA SUELTA TPTE'],
            ['id' => 6, 'name' => 'BOLSA SUELTA COLOR'],
            ['id' => 7, 'name' => 'BOLSA EN ROLLO TPTE'],
            ['id' => 8, 'name' => 'BOLSA EN ROLLO COLOR'],
            ['id' => 9, 'name' => 'ROLLO CORRIDO TPTE'],
            ['id' => 10, 'name' => 'ROLLO CORRIDO COLOR'],
            ['id' => 11, 'name' => 'PELICULA PLANA TPTE'],
            ['id' => 12, 'name' => 'PELICULA PLANA COLOR'],
            ['id' => 13, 'name' => 'TINTAS'],
            ['id' => 14, 'name' => 'DESPERDICIO'],
            ['id' => 15, 'name' => 'EMBALAJE'],
            ['id' => 16, 'name' => 'PELETIZADO'],
            ['id' => 17, 'name' => 'MEZCLAS'],
        ];

        foreach ($classes as $class) {
            ProductClass::firstOrCreate(
                ['slug' => Str::slug($class['name'])],
                ['name' => $class['name']]
            );
        }

        $subclasses = [
            ['id' => 1, 'name' => 'LLDPEF1CA'],
            ['id' => 2, 'name' => 'LLDPEF1SA'],
            ['id' => 3, 'name' => 'LLDPEF2CA'],
            ['id' => 4, 'name' => 'LLDPEF2SA'],
            ['id' => 5, 'name' => 'LDPEF2SA'],
            ['id' => 6, 'name' => 'LDPEF2CA'],
            ['id' => 7, 'name' => 'LDPEF0.25SA'],
            ['id' => 8, 'name' => 'LDPEF0.25CA'],
            ['id' => 9, 'name' => 'HDPE8000'],
            ['id' => 10, 'name' => 'HDPEE924'],
            ['id' => 11, 'name' => 'HDPE0355'],
            ['id' => 12, 'name' => '3PLGCENTRO'],
            ['id' => 13, 'name' => '1.5PLGCENTRO'],
            ['id' => 14, 'name' => 'HDBR'],
        ];

        foreach ($subclasses as $subclass) {
            ProductSubclass::firstOrCreate(
                ['slug' => Str::slug($subclass['name'])],
                ['name' => $subclass['name']]
            );
        }
    }
}
