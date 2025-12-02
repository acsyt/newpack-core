<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transferNumber' => $this->transfer_number,
            'sourceWarehouseId' => $this->source_warehouse_id,
            'destinationWarehouseId' => $this->destination_warehouse_id,
            'status' => $this->status,
            'shippedAt' => $this->shipped_at?->toISOString(),
            'receivedAt' => $this->received_at?->toISOString(),
            'shippedByUserId' => $this->shipped_by_user_id,
            'receivedByUserId' => $this->received_by_user_id,
            'notes' => $this->notes,
            'receivingNotes' => $this->receiving_notes,

            // Campos computados
            'totalItemsCount' => $this->whenLoaded('items', fn() => $this->items->count()),
            'hasDiscrepancies' => $this->whenLoaded('items', function() {
                return $this->items->filter(function($item) {
                    return $item->quantity_received !== null &&
                           $item->quantity_received != $item->quantity_sent;
                })->isNotEmpty();
            }),

            // Relaciones
            'items' => TransferItemResource::collection($this->whenLoaded('items')),
            'sourceWarehouse' => $this->whenLoaded('sourceWarehouse'),
            'destinationWarehouse' => $this->whenLoaded('destinationWarehouse'),
            'shippedByUser' => $this->whenLoaded('shippedByUser'),
            'receivedByUser' => $this->whenLoaded('receivedByUser'),
            'inventoryMovements' => InventoryMovementResource::collection($this->whenLoaded('inventoryMovements')),

            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
