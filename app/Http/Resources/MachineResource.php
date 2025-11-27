<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
