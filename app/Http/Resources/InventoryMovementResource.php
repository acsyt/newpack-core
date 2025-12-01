<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="InventoryMovementResource",
 *     title="Inventory Movement",
 *     description="Inventory movement (transaction) resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="productId", type="integer", example=1),
 *     @OA\Property(property="warehouseId", type="integer", example=1),
 *     @OA\Property(property="warehouseLocationId", type="integer", nullable=true, example=1),
 *     @OA\Property(property="type", type="string", enum={"purchase_entry", "production_output", "production_consumption", "sales_shipment", "adjustment", "transfer"}, example="purchase_entry"),
 *     @OA\Property(property="quantity", type="number", format="float", example=50.0000),
 *     @OA\Property(property="balanceAfter", type="number", format="float", example=150.0000),
 *     @OA\Property(property="batchId", type="integer", nullable=true, example=1),
 *     @OA\Property(property="userId", type="integer", nullable=true, example=1),
 *     @OA\Property(property="referenceType", type="string", nullable=true, example="App\\Models\\Purchase"),
 *     @OA\Property(property="referenceId", type="integer", nullable=true, example=123),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Initial stock"),
 *     @OA\Property(property="product", ref="#/components/schemas/ProductResource"),
 *     @OA\Property(property="warehouse", ref="#/components/schemas/WarehouseResource"),
 *     @OA\Property(property="warehouseLocation", ref="#/components/schemas/WarehouseLocationResource"),
 *     @OA\Property(property="user", ref="#/components/schemas/UserResource"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 */
class InventoryMovementResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'productId' => $this->product_id,
            'warehouseId' => $this->warehouse_id,
            'warehouseLocationId' => $this->warehouse_location_id,
            'type' => $this->type,
            'quantity' => $this->quantity,
            'balanceAfter' => $this->balance_after,
            'batchId' => $this->batch_id,
            'userId' => $this->user_id,
            'referenceType' => $this->reference_type,
            'referenceId' => $this->reference_id,
            'notes' => $this->notes,
            'relatedMovementId' => $this->related_movement_id,

            'product' => $this->whenLoaded('product', function() {
                return new ProductResource($this->product);
            }),
            'warehouse' => $this->whenLoaded('warehouse', function() {
                return new WarehouseResource($this->warehouse);
            }),
            'warehouseLocation' => $this->whenLoaded('warehouseLocation', function() {
                return new WarehouseLocationResource($this->warehouseLocation);
            }),
            'batch' => $this->whenLoaded('batch', function() {
                return $this->batch;
            }),
            'user' => $this->whenLoaded('user', function() {
                return new UserResource($this->user);
            }),
            'reference' => $this->whenLoaded('reference', function() {
                return $this->reference;
            }),
            'relatedMovement' => $this->whenLoaded('relatedMovement', function() {
                return new InventoryMovementResource($this->relatedMovement);
            }),

            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
