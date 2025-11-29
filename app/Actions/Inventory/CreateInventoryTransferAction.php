<?php

namespace App\Actions\Inventory;

use App\Enums\InventoryMovementType;
use App\Exceptions\CustomException;
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
                $sourceStock = InventoryStock::where([
                    'warehouse_id'              => $sourceWarehouseId,
                    'product_id'                => $item['product_id'],
                    'warehouse_location_id'     => $item['source_location_id'] ?? null,
                    'batch_id'                  => $item['batch_id'] ?? null,
                ])->lockForUpdate()->first();

                if (!$sourceStock || $sourceStock->quantity < $item['quantity']) throw new CustomException("Stock insuficiente para el producto ID: {$item['product_id']}");

                $sourceStock->quantity -= $item['quantity'];
                $sourceStock->save();

                $balanceAfterExit = $sourceStock->quantity;

                $exitMovement = InventoryMovement::create([
                    'warehouse_id'          => $sourceWarehouseId,
                    'warehouse_location_id' => $item['source_location_id'] ?? null,
                    'product_id'            => $item['product_id'],
                    'type'                  => InventoryMovementType::EXIT,
                    'quantity'              => $item['quantity'],
                    'batch_id'              => $item['batch_id'] ?? null,
                    'user_id'               => $userId,
                    'notes'                 => $notes ? "Transferencia Salida: $notes" : "Transferencia Salida",
                    'balance_after'         => $balanceAfterExit,
                ]);

                $destStock = InventoryStock::firstOrNew([
                    'warehouse_id'              => $destWarehouseId,
                    'product_id'                => $item['product_id'],
                    'warehouse_location_id'     => $item['destination_location_id'] ?? null,
                    'batch_id'                  => $item['batch_id'] ?? null,
                ]);

                if ($destStock->exists) {
                    $destStock = InventoryStock::where('id', $destStock->id)->lockForUpdate()->firstOrFail();
                }

                $destStock->quantity = ($destStock->quantity ?? 0) + $item['quantity'];
                $destStock->save();

                $balanceAfterEntry = $destStock->quantity;

                $entryMovement = InventoryMovement::create([
                    'warehouse_id'          => $destWarehouseId,
                    'warehouse_location_id' => $item['destination_location_id'] ?? null,
                    'product_id'            => $item['product_id'],
                    'type'                  => InventoryMovementType::ENTRY,
                    'quantity'              => $item['quantity'],
                    'batch_id'              => $item['batch_id'] ?? null,
                    'user_id'               => $userId,
                    'notes'                 => $notes ? "Transferencia Entrada: $notes" : "Transferencia Entrada",
                    'related_movement_id'   => $exitMovement->id,
                    'balance_after'         => $balanceAfterEntry,
                ]);

                $exitMovement->update(['related_movement_id' => $entryMovement->id]);
            }

            DB::commit();
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Inventory Transfer Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new CustomException('Error al procesar la transferencia de inventario.', 500);
        }
    }
}
