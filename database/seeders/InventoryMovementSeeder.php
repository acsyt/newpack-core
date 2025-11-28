<?php

namespace Database\Seeders;

use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Este seeder crea movimientos de inventario (entradas/salidas) realistas.
     * Los movimientos se registran cronológicamente:
     * 1. Entradas por compra (purchase_entry)
     * 2. Salidas por producción (production_consumption)
     * 3. Entradas por producción (production_output)
     * 4. Salidas por ventas (sales_shipment)
     * 5. Ajustes de inventario (adjustment)
     * 6. Transferencias entre almacenes (transfer)
     */
    public function run(): void
    {
        // Obtener datos base
        $products = Product::limit(10)->get();
        $warehouses = Warehouse::all();
        $users = User::limit(3)->get();

        if ($products->isEmpty() || $warehouses->isEmpty() || $users->isEmpty()) {
            $this->command->warn('⚠️  No hay productos, almacenes o usuarios. Ejecuta primero los seeders base.');
            return;
        }

        $warehouse = $warehouses->first();
        $user = $users->first();

        // Obtener ubicaciones del almacén principal
        $locations = WarehouseLocation::where('warehouse_id', $warehouse->id)->get();
        $location = $locations->isNotEmpty() ? $locations->first() : null;

        $this->command->info('🏭 Creando movimientos de inventario...');

        $movements = [];

        // Para cada producto, crear un flujo realista de movimientos
        foreach ($products as $product) {
            $balance = 0;

            // 1. ENTRADA POR COMPRA INICIAL (1000 unidades)
            $quantity = 1000;
            $balance += $quantity;
            $movements[] = [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'warehouse_location_id' => $location?->id,
                'type' => 'purchase_entry',
                'quantity' => $quantity,
                'balance_after' => $balance,
                'user_id' => $user->id,
                'reference_type' => null,
                'reference_id' => null,
                'batch_id' => null,
                'notes' => 'Compra inicial de inventario',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ];

            // 2. ENTRADA POR COMPRA ADICIONAL (500 unidades)
            $quantity = 500;
            $balance += $quantity;
            $movements[] = [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'warehouse_location_id' => $location?->id,
                'type' => 'purchase_entry',
                'quantity' => $quantity,
                'balance_after' => $balance,
                'user_id' => $user->id,
                'reference_type' => null,
                'reference_id' => null,
                'batch_id' => null,
                'notes' => 'Reabastecimiento de almacén',
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(25),
            ];

            // 3. CONSUMO POR PRODUCCIÓN (-300 unidades)
            $quantity = 300;
            $balance -= $quantity;
            $movements[] = [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'warehouse_location_id' => $location?->id,
                'type' => 'production_consumption',
                'quantity' => -$quantity,
                'balance_after' => $balance,
                'user_id' => $user->id,
                'reference_type' => null,
                'reference_id' => null,
                'batch_id' => null,
                'notes' => 'Material consumido en producción',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ];

            // 4. SALIDA POR VENTA (-200 unidades)
            $quantity = 200;
            $balance -= $quantity;
            $movements[] = [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'warehouse_location_id' => $location?->id,
                'type' => 'sales_shipment',
                'quantity' => -$quantity,
                'balance_after' => $balance,
                'user_id' => $user->id,
                'reference_type' => null,
                'reference_id' => null,
                'batch_id' => null,
                'notes' => 'Venta a cliente',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ];

            // 5. ENTRADA POR PRODUCCIÓN (+150 unidades)
            $quantity = 150;
            $balance += $quantity;
            $movements[] = [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'warehouse_location_id' => $location?->id,
                'type' => 'production_output',
                'quantity' => $quantity,
                'balance_after' => $balance,
                'user_id' => $user->id,
                'reference_type' => null,
                'reference_id' => null,
                'batch_id' => null,
                'notes' => 'Producto terminado de producción',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ];

            // 6. AJUSTE DE INVENTARIO (+50 unidades - corrección de faltante)
            $quantity = 50;
            $balance += $quantity;
            $movements[] = [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'warehouse_location_id' => $location?->id,
                'type' => 'adjustment',
                'quantity' => $quantity,
                'balance_after' => $balance,
                'user_id' => $user->id,
                'reference_type' => null,
                'reference_id' => null,
                'batch_id' => null,
                'notes' => 'Ajuste por conteo físico',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ];

            // 7. SALIDA POR VENTA RECIENTE (-100 unidades)
            $quantity = 100;
            $balance -= $quantity;
            $movements[] = [
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'warehouse_location_id' => $location?->id,
                'type' => 'sales_shipment',
                'quantity' => -$quantity,
                'balance_after' => $balance,
                'user_id' => $user->id,
                'reference_type' => null,
                'reference_id' => null,
                'batch_id' => null,
                'notes' => 'Venta reciente',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ];
        }

        // Insertar todos los movimientos
        DB::table('inventory_transactions')->insert($movements);

        $this->command->info('✅ Se crearon ' . count($movements) . ' movimientos de inventario');
        $this->command->info('📊 Saldo final aproximado por producto: 1100 unidades');
    }
}
