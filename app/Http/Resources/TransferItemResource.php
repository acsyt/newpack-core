<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transferId' => $this->transfer_id,
            'productId' => $this->product_id,
            'warehouseLocationSourceId' => $this->warehouse_location_source_id,
            'warehouseLocationDestinationId' => $this->warehouse_location_destination_id,
            'batchId' => $this->batch_id,
            'quantitySent' => number_format($this->quantity_sent, 4),
            'quantityReceived' => $this->quantity_received ? number_format($this->quantity_received, 4) : null,
            'quantityMissing' => $this->quantity_missing ? number_format($this->quantity_missing, 4) : null,
            'quantityDamaged' => $this->quantity_damaged ? number_format($this->quantity_damaged, 4) : null,
            'quantityDiscrepancy' => $this->quantity_discrepancy ? number_format($this->quantity_discrepancy, 4) : null,
            'notes' => $this->notes,

            // Relaciones condicionales
            'product' => $this->whenLoaded('product'),
            'sourceLocation' => $this->whenLoaded('sourceLocation'),
            'destinationLocation' => $this->whenLoaded('destinationLocation'),
            'batch' => $this->whenLoaded('batch'),

            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }
}
