<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="StateResource",
 *     type="object",
 *     title="State",
 *     description="Recurso de Estado que representa la información de un estado y sus ciudades asociadas.",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID del estado",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre del estado",
 *         example="California"
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="string",
 *         description="Código del estado",
 *         example="CA"
 *     ),
 *     @OA\Property(
 *         property="active",
 *         type="boolean",
 *         description="Estado de actividad",
 *         example=true
 *     ),
 * )
*/
class StateResource extends JsonResource
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
            'code'      => $this->code,
            'active'    => $this->active,
            'cities'    => CityResource::collection($this->whenLoaded('cities')),
            'country'   => new CountryResource($this->whenLoaded('country')),
        ];
    }
}
