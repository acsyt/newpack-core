<?php

namespace Database\Seeders;

use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder calcula y crea los registros de inventory_stocks
     * basándose en los movimientos registrados en inventory_movements.
     *
     * El stock actual se calcula sumando todos los movimientos agrupados por:
     * - product_id
     * - warehouse_id
     * - warehouse_location_id
     * - batch_id
     */
    public function run(): void
    {
        $this->command->info('📊 Calculando stocks desde movimientos...');

        // Limpiamos los stocks existentes
        DB::table('inventory_stocks')->truncate();

        // Calculamos los stocks agrupados desde los movimientos
        $stocks = DB::table('inventory_movements')
            ->select(
                'product_id',
                'warehouse_id',
                'warehouse_location_id',
                'batch_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('MAX(created_at) as last_movement')
            )
            ->groupBy('product_id', 'warehouse_id', 'warehouse_location_id', 'batch_id')
            ->having('total_quantity', '>', 0) // Solo stocks positivos
            ->get();

        $stockRecords = [];
        $totalStocks = 0;

        foreach ($stocks as $stock) {
            // Determinar el estado basándose en la cantidad
            $quantity = (float) $stock->total_quantity;
            $status = 'available';

            // Si la cantidad es muy baja, marcarlo como disponible pero podría necesitar reabastecimiento
            // Si fuera negativo, sería damaged (pero lo filtramos con HAVING)
            if ($quantity < 10) {
                // Podrías marcarlo como 'reserved' o mantenerlo 'available'
                $status = 'available';
            }

            $stockRecords[] = [
                'product_id' => $stock->product_id,
                'warehouse_id' => $stock->warehouse_id,
                'warehouse_location_id' => $stock->warehouse_location_id,
                'batch_id' => $stock->batch_id,
                'quantity' => $quantity,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $totalStocks++;
        }

        if (!empty($stockRecords)) {
            // Insertar en lotes de 500 para evitar problemas de memoria
            $chunks = array_chunk($stockRecords, 500);
            foreach ($chunks as $chunk) {
                DB::table('inventory_stocks')->insert($chunk);
            }

            $this->command->info('✅ Se crearon ' . $totalStocks . ' registros de stock');

            // Mostrar resumen
            $totalQuantity = array_sum(array_column($stockRecords, 'quantity'));
            $this->command->info('📦 Cantidad total en stock: ' . number_format($totalQuantity, 2) . ' unidades');
        } else {
            $this->command->warn('⚠️  No se encontraron movimientos para calcular stocks.');
            $this->command->info('    Ejecuta primero: php artisan db:seed --class=InventoryMovementSeeder');
        }
    }
}
