<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MeasureUnitResource",
 *     title="Measure Unit",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Kilogramo"),
 *     @OA\Property(property="code", type="string", example="kg")
 * )
 */
class MeasureUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'code' => $this->code,
        ];
    }
}
