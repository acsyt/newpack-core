<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ZipCodeResource",
 *     type="object",
 *     title="Zip Code",
 *     description="Zip Code resource",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID del código postal",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre del código postal",
 *         example="10001"
 *     ),
 *     @OA\Property(
 *         property="cityId",
 *         type="integer",
 *         description="ID de la ciudad asociada",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="stateId",
 *         type="integer",
 *         description="ID del estado asociado",
 *         example=1
 *     ),
 * )
*/
class ZipCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'cityId'    => $this->city_id,
            'city'      => new CityResource($this->whenLoaded('city')),
            'suburbs'   => SuburbResource::collection($this->whenLoaded('suburbs')),
        ];
    }
}
