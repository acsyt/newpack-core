<?php

namespace App\Actions\Inventory;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use App\Models\InventoryStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateInventoryTransferAction
{
    public static function handle(array $data): void
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();
            $sourceWarehouseId = $data['source_warehouse_id'];
            $destWarehouseId = $data['destination_warehouse_id'];
            $notes = $data['notes'] ?? null;

            foreach ($data['products'] as $item) {
                // ---------------------------------------------------------
                // 1. PROCESO DE SALIDA (SOURCE)
                // ---------------------------------------------------------

                $sourceStock = InventoryStock::where([
                    'warehouse_id' => $sourceWarehouseId,
                    'product_id' => $item['product_id'],
                    'warehouse_location_id' => $item['source_location_id'] ?? null,
                    'batch_id' => $item['batch_id'] ?? null,
                ])->lockForUpdate()->first();

                if (!$sourceStock || $sourceStock->quantity < $item['quantity']) {
                    throw new \App\Exceptions\CustomException("Stock insuficiente para el producto ID: {$item['product_id']}");
                }

                // OPTIMIZACIÓN: Resta manual para asegurar consistencia en memoria
                $sourceStock->quantity -= $item['quantity'];
                $sourceStock->save();

                $balanceAfterExit = $sourceStock->quantity;

                $exitMovement = InventoryMovement::create([
                    'warehouse_id' => $sourceWarehouseId,
                    'warehouse_location_id' => $item['source_location_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'type' => InventoryMovementType::EXIT,
                    'quantity' => $item['quantity'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'user_id' => $userId,
                    'notes' => $notes ? "Transferencia Salida: $notes" : "Transferencia Salida",
                    'balance_after' => $balanceAfterExit,
                ]);

                // ---------------------------------------------------------
                // 2. PROCESO DE ENTRADA (DESTINATION)
                // ---------------------------------------------------------

                $destStock = InventoryStock::firstOrNew([
                    'warehouse_id' => $destWarehouseId,
                    'product_id' => $item['product_id'],
                    'warehouse_location_id' => $item['destination_location_id'] ?? null,
                    'batch_id' => $item['batch_id'] ?? null,
                ]);

                // Bloqueo pesimista si ya existe
                if ($destStock->exists) {
                    $destStock = InventoryStock::where('id', $destStock->id)->lockForUpdate()->firstOrFail();
                } else {
                    // SEGURIDAD: Si es nuevo, copiamos datos estructurales del origen
                    // Esto evita errores si tu tabla requiere unidad de medida u otros campos
                    $destStock->measure_unit_id = $sourceStock->measure_unit_id ?? 1;
                    $destStock->product_type_id = $sourceStock->product_type_id ?? null;
                }

                $destStock->quantity = ($destStock->quantity ?? 0) + $item['quantity'];
                $destStock->save();

                $balanceAfterEntry = $destStock->quantity;

                $entryMovement = InventoryMovement::create([
                    'warehouse_id' => $destWarehouseId,
                    'warehouse_location_id' => $item['destination_location_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'type' => InventoryMovementType::ENTRY,
                    'quantity' => $item['quantity'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'user_id' => $userId,
                    'notes' => $notes ? "Transferencia Entrada: $notes" : "Transferencia Entrada",
                    'related_movement_id' => $exitMovement->id,
                    'balance_after' => $balanceAfterEntry,
                ]);

                // Linkear la salida a la entrada
                $exitMovement->update(['related_movement_id' => $entryMovement->id]);
            }

            DB::commit();
        } catch (\App\Exceptions\CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Inventory Transfer Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new \App\Exceptions\CustomException('Error al procesar la transferencia de inventario.', 500);
        }
    }
}
