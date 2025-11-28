<?php

namespace Database\Seeders;

use App\Models\ProductClass;
use App\Models\ProductSubclass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classes = [
            ['code' => 'RES', 'name' => 'RESINAS'],
            ['code' => 'ADT', 'name' => 'ADITIVOS'],
            ['code' => 'PIG', 'name' => 'PIGMENTOS'],
            ['code' => 'BOB', 'name' => 'BOBINAS'],
            ['code' => 'BST', 'name' => 'BOLSA SUELTA TPTE'],
            ['code' => 'BSC', 'name' => 'BOLSA SUELTA COLOR'],
            ['code' => 'BRT', 'name' => 'BOLSA EN ROLLO TPTE'],
            ['code' => 'BRC', 'name' => 'BOLSA EN ROLLO COLOR'],
            ['code' => 'RCT', 'name' => 'ROLLO CORRIDO TPTE'],
            ['code' => 'RCC', 'name' => 'ROLLO CORRIDO COLOR'],
            ['code' => 'PPT', 'name' => 'PELICULA PLANA TPTE'],
            ['code' => 'PPC', 'name' => 'PELICULA PLANA COLOR'],
            ['code' => 'TIN', 'name' => 'TINTAS'],
            ['code' => 'DES', 'name' => 'DESPERDICIO'],
            ['code' => 'EMB', 'name' => 'EMBALAJE'],
            ['code' => 'PEL', 'name' => 'PELETIZADO'],
            ['code' => 'MEZ', 'name' => 'MEZCLAS'],
        ];

        foreach ($classes as $class) {
            ProductClass::firstOrCreate(
                ['code' => $class['code']],
                [
                    'code' => $class['code'],
                    'slug' => Str::slug($class['name']),
                    'name' => $class['name']
                ]
            );
        }

        $subclasses = [
            ['code' => 'LLDPEF1CA', 'name' => 'LLDPEF1CA'],
            ['code' => 'LLDPEF1SA', 'name' => 'LLDPEF1SA'],
            ['code' => 'LLDPEF2CA', 'name' => 'LLDPEF2CA'],
            ['code' => 'LLDPEF2SA', 'name' => 'LLDPEF2SA'],
            ['code' => 'LDPEF2SA', 'name' => 'LDPEF2SA'],
            ['code' => 'LDPEF2CA', 'name' => 'LDPEF2CA'],
            ['code' => 'LDPEF0.25SA', 'name' => 'LDPEF0.25SA'],
            ['code' => 'LDPEF0.25CA', 'name' => 'LDPEF0.25CA'],
            ['code' => 'HDPE8000', 'name' => 'HDPE8000'],
            ['code' => 'HDPEE924', 'name' => 'HDPEE924'],
            ['code' => 'HDPE0355', 'name' => 'HDPE0355'],
            ['code' => '3PLGCENTRO', 'name' => '3PLGCENTRO'],
            ['code' => '1.5PLGCENTRO', 'name' => '1.5PLGCENTRO'],
            ['code' => 'HDBR', 'name' => 'HDBR'],
        ];

        foreach ($subclasses as $subclass) {
            ProductSubclass::firstOrCreate(
                ['code' => $subclass['code']],
                [
                    'code' => $subclass['code'],
                    'slug' => Str::slug($subclass['name']),
                    'name' => $subclass['name']
                ]
            );
        }
    }
}
