<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SuburbResource",
 *     type="object",
 *     title="Suburb",
 *     description="Suburb resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID del suburb",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre del suburb",
 *         example="Centro"
 *     ),
 *     @OA\Property(
 *         property="zipCodeId",
 *         type="integer",
 *         description="ID del código postal asociado",
 *         example=101
 *     ),
 * )
*/
class SuburbResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id'        => $this->id,
            'name'      => $this->name,
            'zipCodeId' => $this->zip_code_id,
            'zipCode'   => new ZipCodeResource($this->whenLoaded('zipCode')),
        ];
    }
}
