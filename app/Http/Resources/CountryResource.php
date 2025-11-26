<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CountryResource",
 *     description="Recurso de País para representar los datos de un país.",
 *     type="object",
 *     title="Country",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID del país",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="code",
 *         type="string",
 *         description="Código del país",
 *         example="MX"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Nombre del país",
 *         example="México"
 *     )
 * )
*/
class CountryResource extends JsonResource
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
            'code'      => $this->code,
            'name'      => $this->name,
        ];
    }
}
