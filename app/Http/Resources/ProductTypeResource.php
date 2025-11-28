<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProductTypeResource",
 *     title="Product Type",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Materia Prima"),
 *     @OA\Property(property="code", type="string", example="MP"),
 *     @OA\Property(property="slug", type="string", example="materia-prima")
 * )
 */
class ProductTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'slug' => $this->slug,
        ];
    }
}
