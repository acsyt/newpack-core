<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Product;
use App\Models\ProductType;
use App\Models\MeasureUnit;
use App\Models\ProductClass;
use App\Models\ProductSubclass;

use Illuminate\Support\Str;

class RawMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $path = resource_path('json/raw-materials.data.json');
        $products = json_decode(file_get_contents($path), true);

        $kgUnit = MeasureUnit::where('code', 'kg')->first();
        $defaultUnitId = $kgUnit ? $kgUnit->id : 1;

        foreach ($products as $productData) {
            $productType = ProductType::where('code', $productData['productTypeCode'])->first();
            $measureUnit = MeasureUnit::where('code', $productData['measureUnitCode'])->first();
            $productClass = ProductClass::where('code', $productData['groupCode'])->first();
            $productSubclass = ProductSubclass::where('code', $productData['subGroupCode'])->first();

            Product::updateOrCreate(
                ['sku' => $productData['code']],
                [
                    'name'                  => $productData['name'],
                    'slug'                  => Str::slug($productData['name']),
                    'sku'                   => $productData['code'],
                    'type'                  => $productType ? $productType->code : ($productData['productTypeCode'] ?? 'MP'),
                    'measure_unit_id'       => $measureUnit ? $measureUnit->id : $defaultUnitId,
                    'product_class_id'      => $productClass ? $productClass->id : null,
                    'product_subclass_id'   => $productSubclass ? $productSubclass->id : null,
                    'track_batches'         => $productData['batch'] ?? false,
                    'is_purchasable'        => true,
                    'is_sellable'           => false,
                    'current_stock'         => 0,
                    'average_cost'          => 0,
                ]
            );
        }
    }
}
