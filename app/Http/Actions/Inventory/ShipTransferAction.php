<?php

namespace App\Http\Actions\Inventory;
use App\Models\Inventory;
use App\Enums\InventoryMovementType;
use App\Exceptions\CustomException;
use App\Models\InventoryMovement;
use App\Models\InventoryStock;
use App\Models\Transfer;
use App\StateMachines\TransferStatusStateMachine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipTransferAction
{
    public static function handle(array $data, int $userId): Transfer
    {
        try {
            return DB::transaction(function () use ($data, $userId) {
                $transfer = self::createTransferRequest($data, $userId);

                self::processShipment($transfer, $userId);

                return $transfer->refresh()->load(['items.product', 'sourceWarehouse', 'destinationWarehouse']);
            });
        } catch (CustomException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Ship Transfer Error: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw new CustomException('Error al procesar el envío de la transferencia.', 500);
        }
    }

    private static function createTransferRequest(array $data, int $userId): Transfer {
        $transfer = Transfer::create([
            'transfer_number'               => Transfer::generateTransferNumber(),
            'source_warehouse_id'           => $data['source_warehouse_id'],
            'destination_warehouse_id'      => $data['destination_warehouse_id'],
            'shipped_by_user_id'            => $userId,
            'notes'                         => $data['notes'] ?? null,
        ]);
        $transfer->status()->transitionTo(TransferStatusStateMachine::REQUESTED);

        foreach ($data['products'] as $item) {
            $transfer->items()->create([
                'product_id'                            => $item['product_id'],
                'warehouse_location_source_id'          => $item['source_location_id'] ?? null,
                'warehouse_location_destination_id'     => $item['destination_location_id'] ?? null,
                'batch_id'                              => $item['batch_id'] ?? null,
                'quantity_sent'                         => $item['quantity'],
                'quantity_received'                     => 0,
            ]);
        }

        return $transfer;
    }

    private static function processShipment(Transfer $transfer, int $userId): void
    {
        foreach ($transfer->items as $item) {
            $stock = InventoryStock::where('warehouse_id', $transfer->source_warehouse_id)
                ->where('product_id', $item->product_id)
                ->where('warehouse_location_id', $item->warehouse_location_source_id)
                ->where('batch_id', $item->batch_id)
                ->lockForUpdate()
                ->first();

            if (!$stock || $stock->quantity < $item->quantity_sent) {
                throw new CustomException("Stock insuficiente en origen para el producto ID: {$item->product_id}");
            }
            $stock->quantity -= $item->quantity_sent;
            $stock->save();

            InventoryMovement::create([
                'warehouse_id'              => $transfer->source_warehouse_id,
                'warehouse_location_id'     => $item->warehouse_location_source_id,
                'product_id'                => $item->product_id,
                'type'                      => InventoryMovementType::EXIT,
                'quantity'                  => $item->quantity_sent,
                'batch_id'                  => $item->batch_id,
                'user_id'                   => $userId,
                'notes'                     => "Envío Transferencia #{$transfer->transfer_number}",
                'balance_after'             => $stock->quantity,
                'transfer_id'               => $transfer->id,
            ]);
        }

        $transfer->status()->transitionTo(TransferStatusStateMachine::SHIPPED);
        $transfer->shipped_at = now();
        $transfer->save();
    }
}
