<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;


/**
 * @OA\Schema(
 *     schema="CurrencyResource",
 *     title="Currency",
 *     description="Currency resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="US Dollar"),
 *     @OA\Property(property="code", type="string", example="USD"),
 *     @OA\Property(property="active", type="boolean", example=true),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 */
class CurrencyResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'active' => $this->active,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
