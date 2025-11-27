<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="ProductSubclassResource",
 *     type="object",
 *     title="Product Subclass Resource",
 *     description="Product subclass resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="SC-001"),
 *     @OA\Property(property="name", type="string", example="HDPE"),
 *     @OA\Property(property="description", type="string", example="High-density polyethylene"),
 *     @OA\Property(property="slug", type="string", example="hdpe"),
 *     @OA\Property(property="productClassId", type="integer", example=1),
 *     @OA\Property(property="productClass", ref="#/components/schemas/ProductClassResource"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 */
class ProductSubclassResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'productClassId' => $this->product_class_id,
            'productClass' => $this->whenLoaded('productClass', function () {
                return [
                    'id' => $this->productClass->id,
                    'code' => $this->productClass->code,
                    'name' => $this->productClass->name,
                ];
            }),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
