<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="ProcessResource",
 *     type="object",
 *     title="Process Resource",
 *     description="Process resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="PROC-001"),
 *     @OA\Property(property="name", type="string", example="Molding"),
 *     @OA\Property(property="appliesToPt", type="boolean", example=true),
 *     @OA\Property(property="appliesToMp", type="boolean", example=false),
 *     @OA\Property(property="appliesToCompounds", type="boolean", example=true),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 */
class ProcessResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'appliesToPt' => $this->applies_to_pt,
            'appliesToMp' => $this->applies_to_mp,
            'appliesToCompounds' => $this->applies_to_compounds,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
