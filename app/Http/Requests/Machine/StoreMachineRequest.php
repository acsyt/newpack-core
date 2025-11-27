<?php

namespace App\Http\Requests\Machine;

use Illuminate\Foundation\Http\FormRequest;

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
}
