<?php

namespace App\Http\Requests\Machine;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="StoreMachineRequest",
 *     type="object",
 *     required={"code", "name", "processId"},
 *     @OA\Property(property="code", type="string", example="M-001", description="Unique machine code"),
 *     @OA\Property(property="name", type="string", example="Extruder 1", description="Machine name"),
 *     @OA\Property(property="processId", type="integer", example=1, description="Process ID"),
 *     @OA\Property(property="speedMh", type="number", format="float", example=100.5, description="Speed in M/H"),
 *     @OA\Property(property="speedKgh", type="number", format="float", example=150.75, description="Speed in KG/H"),
 *     @OA\Property(property="circumferenceTotal", type="number", format="float", example=300.0, description="Total circumference"),
 *     @OA\Property(property="maxWidth", type="number", format="float", example=200.0, description="Maximum width"),
 *     @OA\Property(property="maxCenter", type="number", format="float", example=180.0, description="Maximum center"),
 *     @OA\Property(property="status", type="string", example="active", description="Machine status")
 * )
 */
class StoreMachineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:machines,code'],
            'name' => ['required', 'string', 'max:255'],
            'processId' => ['required', 'integer', 'exists:processes,id'],
            'speedMh' => ['nullable', 'numeric'],
            'speedKgh' => ['nullable', 'numeric'],
            'circumferenceTotal' => ['nullable', 'numeric'],
            'maxWidth' => ['nullable', 'numeric'],
            'maxCenter' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();

        return [
            'code' => $validated['code'],
            'name' => $validated['name'],
            'process_id' => $validated['processId'],
            'speed_mh' => $validated['speedMh'] ?? null,
            'speed_kgh' => $validated['speedKgh'] ?? null,
            'circumference_total' => $validated['circumferenceTotal'] ?? null,
            'max_width' => $validated['maxWidth'] ?? null,
            'max_center' => $validated['maxCenter'] ?? null,
            'status' => $validated['status'] ?? 'active',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The machine code is required.',
            'code.unique' => 'A machine with this code already exists.',
            'name.required' => 'The machine name is required.',
            'processId.required' => 'The process is required.',
            'processId.exists' => 'The selected process does not exist.',
        ];
    }
}
