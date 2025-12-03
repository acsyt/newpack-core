<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductIngredientResource",
 *     title="ProductIngredient",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Polietileno de Baja Densidad"),
 *     @OA\Property(property="sku", type="string", example="MP-PEBD-001"),
 *     @OA\Property(property="type", type="string", example="raw_material"),
 *     @OA\Property(property="quantity", type="number", format="float", example=0.5),
 *     @OA\Property(property="wastagePercent", type="number", format="float", example=2.0),
 *     @OA\Property(property="processStage", type="string", nullable=true, example="EXTRUSION"),
 *     @OA\Property(property="isActive", type="boolean", example=true)
 * )
 */
class ProductIngredientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'sku'               => $this->sku,
            'type'              => $this->productType?->code ?? null,
            'measureUnit'       => $this->measureUnit ? [
                'id' => $this->measureUnit->id,
                'name' => $this->measureUnit->name,
                'code' => $this->measureUnit->code,
            ] : null,
            'measureUnitId'     => $this->measure_unit_id,

            // Pivot data from product_compounds table
            'quantity'          => $this->pivot->quantity ? (float) $this->pivot->quantity : null,
            'wastagePercent'    => $this->pivot->wastage_percent ? (float) $this->pivot->wastage_percent : null,
            'processStage'      => $this->pivot->process_stage,
            'isActive'          => $this->pivot->is_active ? (bool) $this->pivot->is_active : true,
        ];
    }
}
