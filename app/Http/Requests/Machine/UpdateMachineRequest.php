<?php

namespace App\Http\Requests\Machine;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateMachineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $machineId = $this->route('machine');

        return [
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('machines', 'code')->ignore($machineId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'processId' => ['sometimes', 'integer', 'exists:processes,id'],
            'speedMh' => ['sometimes', 'nullable', 'numeric'],
            'speedKgh' => ['sometimes', 'nullable', 'numeric'],
            'circumferenceTotal' => ['sometimes', 'nullable', 'numeric'],
            'maxWidth' => ['sometimes', 'nullable', 'numeric'],
            'maxCenter' => ['sometimes', 'nullable', 'numeric'],
            'status' => ['sometimes', 'nullable', 'string', 'max:50'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        $mapped = [];

        if (isset($validated['code'])) {
            $mapped['code'] = $validated['code'];
        }

        if (isset($validated['name'])) {
            $mapped['name'] = $validated['name'];
        }

        if (isset($validated['processId'])) {
            $mapped['process_id'] = $validated['processId'];
        }

        if (isset($validated['speedMh'])) {
            $mapped['speed_mh'] = $validated['speedMh'];
        }

        if (isset($validated['speedKgh'])) {
            $mapped['speed_kgh'] = $validated['speedKgh'];
        }

        if (isset($validated['circumferenceTotal'])) {
            $mapped['circumference_total'] = $validated['circumferenceTotal'];
        }

        if (isset($validated['maxWidth'])) {
            $mapped['max_width'] = $validated['maxWidth'];
        }

        if (isset($validated['maxCenter'])) {
            $mapped['max_center'] = $validated['maxCenter'];
        }

        if (isset($validated['status'])) {
            $mapped['status'] = $validated['status'];
        }

        return $mapped;
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'A machine with this code already exists.',
            'processId.exists' => 'The selected process does not exist.',
        ];
    }
}
