<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   schema="WarehouseLocationResource",
 *   title="Warehouse Location",
 *   description="Warehouse Location resource",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="uniqueId", type="string", format="uuid", example="550e8400-e29b-41d4-a716-446655440000"),
 *   @OA\Property(property="warehouseId", type="integer", example=1),
 *   @OA\Property(property="warehouse", ref="#/components/schemas/WarehouseResource", nullable=true),
 *   @OA\Property(property="aisle", type="string", nullable=true, example="A-01"),
 *   @OA\Property(property="shelf", type="string", nullable=true, example="S-02"),
 *   @OA\Property(property="section", type="string", nullable=true, example="L-03"),
 *   @OA\Property(property="createdAt", type="string", format="date-time"),
 *   @OA\Property(property="updatedAt", type="string", format="date-time"),
 * )
 */
class WarehouseLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'uniqueId'      => $this->unique_id,
            'warehouseId'   => $this->warehouse_id,
            'warehouse'     => new WarehouseResource($this->whenLoaded('warehouse')),
            'aisle'         => $this->aisle,
            'shelf'         => $this->shelf,
            'section'       => $this->section,
            'createdAt'     => $this->created_at,
            'updatedAt'     => $this->updated_at,
        ];
    }
}
