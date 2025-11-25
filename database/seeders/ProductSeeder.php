<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Enums\ProductType;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CREACIÓN DE MATERIAS PRIMAS (Ingredientes)

        $polietileno = Product::factory()->create([
            'name' => 'Polietileno de Baja Densidad (PEBD)',
            'sku' => 'MP-PEBD-001',
            'type' => ProductType::RAW_MATERIAL,
            'unit_of_measure' => 'kg',
            'average_cost' => 25.50,
            'track_batches' => true,
            'is_purchasable' => true,
            'is_sellable' => false,
            'current_stock' => 5000,
        ]);

        $pigmentoNegro = Product::factory()->create([
            'name' => 'Pigmento Masterbatch Negro',
            'sku' => 'MP-PIG-BLK',
            'type' => ProductType::RAW_MATERIAL,
            'unit_of_measure' => 'kg',
            'average_cost' => 150.00,
            'track_batches' => true,
            'is_purchasable' => true,
            'is_sellable' => false,
            'current_stock' => 200,
        ]);

        $tintaBlanca = Product::factory()->create([
            'name' => 'Tinta Flexográfica Blanca',
            'sku' => 'MP-INK-WHT',
            'type' => ProductType::RAW_MATERIAL,
            'unit_of_measure' => 'kg',
            'average_cost' => 85.00,
            'track_batches' => true,
            'is_purchasable' => true,
            'is_sellable' => false,
            'current_stock' => 100,
        ]);

        // 2. CREACIÓN DE COMPUESTOS (Productos Terminados)

        // Caso A: Bolsa de Basura (Solo Extrusión)
        $bolsaNegra = Product::factory()->create([
            'name' => 'Bolsa Basura Negra 50x70 Calibre 200',
            'sku' => 'PT-BOL-NEG-5070',
            'type' => ProductType::COMPOUND,
            'unit_of_measure' => 'pza',
            'average_cost' => 0,
            'track_batches' => true,
            'is_purchasable' => false,
            'is_sellable' => true,
        ]);

        // Definición de Receta (BOM)
        $bolsaNegra->ingredients()->attach([
            $polietileno->id => [
                'quantity' => 0.048, // 48 gramos de plástico
                'wastage_percent' => 2.0,
                'process_stage' => 'EXTRUSION' // Se consume en extrusión
            ],
            $pigmentoNegro->id => [
                'quantity' => 0.002, // 2 gramos de pigmento
                'wastage_percent' => 0.5,
                'process_stage' => 'EXTRUSION' // Se consume en extrusión
            ]
        ]);

        // Caso B: Bolsa Boutique (Extrusión + Impresión)
        $bolsaImpresa = Product::factory()->create([
            'name' => 'Bolsa Boutique Impresa 30x40',
            'sku' => 'PT-BOL-IMP-3040',
            'type' => ProductType::COMPOUND,
            'unit_of_measure' => 'pza',
            'is_purchasable' => false,
            'is_sellable' => true,
        ]);

        $bolsaImpresa->ingredients()->attach([
            $polietileno->id => [
                'quantity' => 0.030,
                'wastage_percent' => 2.0,
                'process_stage' => 'EXTRUSION'
            ],
            // La tinta NO se consume en extrusión, se consume en la impresora
            $tintaBlanca->id => [
                'quantity' => 0.005,
                'wastage_percent' => 5.0,
                'process_stage' => 'IMPRESION'
            ]
        ]);

        // 3. DATOS DE RELLENO
        Product::factory(10)->rawMaterial()->create();
        Product::factory(5)->compound()->create();
    }
}
