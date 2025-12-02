<?php

namespace App\Http\Actions\Inventory;

use App\Enums\InventoryMovementType;
use App\Exceptions\CustomException;
use App\Models\InventoryMovement;
use App\Models\InventoryStock;
use App\Models\Transfer;
use App\Models\TransferItem;
use App\StateMachines\TransferStatusStateMachine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceiveTransferAction
{
    public static function handle(int $transferId, array $data): Transfer
    {
        DB::beginTransaction();
        try {
            $userId = Auth::id();

            $transfer = Transfer::lockForUpdate()->findOrFail($transferId);


            if (!$transfer->status()->is(TransferStatusStateMachine::SHIPPED)) throw new CustomException("La transferencia debe estar en estado 'Enviado' para ser recibida.");

            $receivingNotes = $data['receiving_notes'] ?? null;

            foreach ($data['items'] as $itemData) {
                $transferItem = TransferItem::lockForUpdate()->findOrFail($itemData['transfer_item_id']);

                if ($transferItem->transfer_id !== $transfer->id) throw new CustomException("El item no pertenece a esta transferencia.");

                $quantityReceived = $itemData['quantity_received'];
                $quantityMissing = $itemData['quantity_missing'] ?? 0;
                $quantityDamaged = $itemData['quantity_damaged'] ?? 0;

                $transferItem->quantity_received = $quantityReceived;
                $transferItem->quantity_missing = $quantityMissing;
                $transferItem->quantity_damaged = $quantityDamaged;
                $transferItem->save();

                if ($quantityReceived > 0) {
                    $destStock = InventoryStock::firstOrNew([
                        'warehouse_id'                  => $transfer->destination_warehouse_id,
                        'product_id'                    => $transferItem->product_id,
                        'warehouse_location_id'         => $transferItem->warehouse_location_destination_id,
                        'batch_id'                      => $transferItem->batch_id,
                    ]);

                    if ($destStock->exists) {
                        $destStock = InventoryStock::where('id', $destStock->id)->lockForUpdate()->firstOrFail();
                    }

                    $destStock->quantity = ($destStock->quantity ?? 0) + $quantityReceived;
                    $destStock->save();

                    $balanceAfterEntry = $destStock->quantity;

                    InventoryMovement::create([
                        'warehouse_id'              => $transfer->destination_warehouse_id,
                        'warehouse_location_id'     => $transferItem->warehouse_location_destination_id,
                        'product_id'                => $transferItem->product_id,
                        'type'                      => InventoryMovementType::ENTRY,
                        'quantity'                  => $quantityReceived,
                        'batch_id'                  => $transferItem->batch_id,
                        'user_id'                   => $userId,
                        'notes'                     => "Transferencia #{$transfer->transfer_number} - Recepción",
                        'balance_after'             => $balanceAfterEntry,
                        'transfer_id'               => $transfer->id,
                    ]);
                }
            }

            $transfer->status()->transitionTo(TransferStatusStateMachine::COMPLETED);
            $transfer->received_at = now();
            $transfer->received_by_user_id = $userId;
            $transfer->receiving_notes = $receivingNotes;
            $transfer->save();

            DB::commit();
            return $transfer->load(['items.product', 'sourceWarehouse', 'destinationWarehouse']);
        } catch (CustomException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Receive Transfer Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new CustomException('Error al recibir la transferencia de inventario.', 500);
        }
    }
}
