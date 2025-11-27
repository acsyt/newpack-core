<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     title="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Polietileno de Baja Densidad"),
 *     @OA\Property(property="sku", type="string", example="MP-PEBD-001"),
 *     @OA\Property(property="type", type="string", example="raw_material"),
 *     @OA\Property(property="unitOfMeasure", type="string", example="kg"),
 *     @OA\Property(property="averageCost", type="number", format="float", example=25.50),
 *     @OA\Property(property="lastPurchasePrice", type="number", format="float", nullable=true, example=30.00),
 *     @OA\Property(property="currentStock", type="number", format="float", example=1000.00),
 *     @OA\Property(property="minStock", type="number", format="float", example=100.00),
 *     @OA\Property(property="maxStock", type="number", format="float", nullable=true, example=5000.00),
 *     @OA\Property(property="isActive", type="boolean", example=true),
 *     @OA\Property(property="isSellable", type="boolean", example=false),
 *     @OA\Property(property="isPurchasable", type="boolean", example=true),
 *     @OA\Property(property="createdAt", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *     @OA\Property(
 *         property="ingredients",
 *         type="array",
 *         description="Only included when requested",
 *         @OA\Items(ref="#/components/schemas/ProductIngredientResource")
 *     )
 * )
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'sku'                   => $this->sku,
            'type'                  => $this->type->value,
            'measureUnit'           => $this->whenLoaded('measureUnit', function () {
                return [
                    'id' => $this->measureUnit->id,
                    'name' => $this->measureUnit->name,
                    'code' => $this->measureUnit->code,
                ];
            }),
            'measureUnitId'         => $this->measure_unit_id,
            'averageCost'           => (float) $this->average_cost,
            'lastPurchasePrice'     => $this->last_purchase_price ? (float) $this->last_purchase_price : null,
            'currentStock'          => (float) $this->current_stock,
            'minStock'              => (float) $this->min_stock,
            'maxStock'              => $this->max_stock ? (float) $this->max_stock : null,
            'isActive'              => (bool) $this->is_active,
            'isSellable'            => (bool) $this->is_sellable,
            'isPurchasable'         => (bool) $this->is_purchasable,
            'createdAt'             => $this->created_at?->toISOString(),
            'updatedAt'             => $this->updated_at?->toISOString(),

            // Conditional includes
            'ingredients'           => ProductIngredientResource::collection($this->whenLoaded('ingredients')),
        ];
    }
}
