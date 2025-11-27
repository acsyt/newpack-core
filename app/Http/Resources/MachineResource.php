<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="MachineResource",
 *     type="object",
 *     title="Machine Resource",
 *     description="Machine resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="code", type="string", example="M-001"),
 *     @OA\Property(property="name", type="string", example="Extruder 1"),
 *     @OA\Property(property="processId", type="integer", example=1),
 *     @OA\Property(property="process", type="object"),
 *     @OA\Property(property="speedMh", type="number", format="float", example=100.5),
 *     @OA\Property(property="speedKgh", type="number", format="float", example=150.75),
 *     @OA\Property(property="circumferenceTotal", type="number", format="float", example=300.0),
 *     @OA\Property(property="maxWidth", type="number", format="float", example=200.0),
 *     @OA\Property(property="maxCenter", type="number", format="float", example=180.0),
 *     @OA\Property(property="status", type="string", example="active"),
 *     @OA\Property(property="createdAt", type="string", format="date-time"),
 *     @OA\Property(property="updatedAt", type="string", format="date-time")
 * )
 */
class MachineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'processId' => $this->process_id,
            'process' => $this->whenLoaded('process', function () {
                return [
                    'id' => $this->process->id,
                    'code' => $this->process->code,
                    'name' => $this->process->name,
                ];
            }),
            'speedMh' => $this->speed_mh,
            'speedKgh' => $this->speed_kgh,
            'circumferenceTotal' => $this->circumference_total,
            'maxWidth' => $this->max_width,
            'maxCenter' => $this->max_center,
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
