<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductType;
use Illuminate\Support\Str;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $path = resource_path('json/product-types.data.json');
        $types = json_decode(file_get_contents($path), true);
        foreach ($types as $type) {
            ProductType::updateOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'code' => $type['code'],
                    'slug' => Str::slug($type['name']),
                ]
            );
        }
    }
}
