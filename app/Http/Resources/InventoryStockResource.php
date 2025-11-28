<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="InventoryStockResource",
 *     title="Inventory Stock",
 *     description="Inventory stock resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="productId", type="integer", example=1),
 *     @OA\Property(property="warehouseId", type="integer", example=1),
 *     @OA\Property(property="warehouseLocationId", type="integer", nullable=true, example=1),
 *     @OA\Property(property="batchId", type="integer", nullable=true, example=1),
 *     @OA\Property(property="quantity", type="number", format="float", example=100.5000),
 *     @OA\Property(property="status", type="string", enum={"available", "reserved", "damaged"}, example="available"),
 *     @OA\Property(property="product", ref="#/components/schemas/ProductResource"),
 *     @OA\Property(property="warehouse", ref="#/components/schemas/WarehouseResource"),
 *     @OA\Property(property="warehouseLocation", ref="#/components/schemas/WarehouseLocationResource"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 */
class InventoryStockResource extends JsonResource
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
            'batchId' => $this->batch_id,
            'quantity' => $this->quantity,
            'status' => $this->status,

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

            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
