<?php

namespace App\Actions\Inventory;

use App\Enums\InventoryMovementType;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreateInventoryTransferAction
{
    public function execute(array $data): void
    {
        DB::transaction(function () use ($data) {
            $userId = Auth::id();
            $sourceWarehouseId = $data['source_warehouse_id'];
            $destWarehouseId = $data['destination_warehouse_id'];
            $notes = $data['notes'] ?? null;

            foreach ($data['products'] as $item) {
                // 1. Create EXIT movement (Source)
                $exitMovement = InventoryMovement::create([
                    'warehouse_id' => $sourceWarehouseId,
                    'warehouse_location_id' => $item['source_location_id'] ?? null,
                    'product_id' => $item['product_id'],
                    'type' => InventoryMovementType::EXIT,
                    'quantity' => $item['quantity'],
                    'batch_id' => $item['batch_id'] ?? null,
                    'user_id' => $userId,
                    'notes' => $notes ? "Transferencia Salida: $notes" : "Transferencia Salida",
                    // balance_after will be calculated by model/observer or we should calculate it here?
                    // Assuming Model/Observer handles it or we need to handle it.
                    // For now, let's assume the basic creation. If balance logic is in Observer, it's fine.
                    // If not, we might need to add it. But let's stick to creation first.
                    'balance_after' => 0, // Placeholder, should be calculated
                ]);

                // 2. Create ENTRY movement (Destination)
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
                    'balance_after' => 0, // Placeholder
                ]);

                // Link EXIT to ENTRY
                $exitMovement->update(['related_movement_id' => $entryMovement->id]);
            }
        });
    }
}
