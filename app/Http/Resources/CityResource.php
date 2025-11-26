<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CityResource",
 *     description="Recurso de Ciudad para representar los datos de la ciudad con relaciones.",
 *     type="object",
 *     title="City",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID de la ciudad",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre de la ciudad",
 *         example="Los Angeles"
 *     ),
 *     @OA\Property(
 *         property="stateId",
 *         type="integer",
 *         description="ID del estado al que pertenece la ciudad",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="active",
 *         type="boolean",
 *         description="Indica si la ciudad está activa",
 *         example=true
 *     ),
 * )
*/
class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'stateId'       => $this->state_id,
            'state'         => new StateResource($this->whenLoaded('state')),
            'active'        => $this->active,
            'states'        => StateResource::collection($this->whenLoaded('states')),
        ];
    }
}
