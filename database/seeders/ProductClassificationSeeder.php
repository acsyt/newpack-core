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
        // 1. Seed Subclasses from sub-groups.data.json
        $subGroupsPath = resource_path('json/sub-groups.data.json');
        $subGroups = json_decode(file_get_contents($subGroupsPath), true);

        foreach ($subGroups as $subGroup) {
            ProductSubclass::updateOrCreate(
                ['code' => $subGroup['code']],
                [
                    'name' => $subGroup['name'],
                    'code' => $subGroup['code'],
                    'slug' => Str::slug($subGroup['name']),
                ]
            );
        }

        // 2. Seed Classes from raw-materials.data.json (extract unique groupCodes)
        $rawMaterialsPath = resource_path('json/raw-materials.data.json');
        $rawMaterials = json_decode(file_get_contents($rawMaterialsPath), true);

        $uniqueGroups = [];
        foreach ($rawMaterials as $material) {
            $groupCode = $material['groupCode'];
            if ($groupCode && !isset($uniqueGroups[$groupCode])) {
                $uniqueGroups[$groupCode] = $groupCode; // Use code as name for now if no other source
            }
        }

        foreach ($uniqueGroups as $groupCode => $groupName) {
            ProductClass::updateOrCreate(
                ['code' => $groupCode],
                [
                    'name' => $groupName, // We use the code as name since we don't have a separate name source
                    'code' => $groupCode,
                    'slug' => Str::slug($groupName),
                ]
            );
        }

        // 3. Link Subclasses to Classes based on raw-materials.data.json
        foreach ($rawMaterials as $material) {
            $groupCode = $material['groupCode'];
            $subGroupCode = $material['subGroupCode'];

            if ($groupCode && $subGroupCode) {
                $class = ProductClass::where('code', $groupCode)->first();
                $subclass = ProductSubclass::where('code', $subGroupCode)->first();

                if ($class && $subclass) {
                    $subclass->product_class_id = $class->id;
                    $subclass->save();
                }
            }
        }
    }
}
