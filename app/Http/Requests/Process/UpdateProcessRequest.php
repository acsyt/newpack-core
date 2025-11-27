<?php

namespace App\Http\Requests\Process;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $processId = $this->route('process');

        return [
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('processes', 'code')->ignore($processId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'appliesToPt' => ['sometimes', 'boolean'],
            'appliesToMp' => ['sometimes', 'boolean'],
            'appliesToCompounds' => ['sometimes', 'boolean'],
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

        if (isset($validated['appliesToPt'])) {
            $mapped['applies_to_pt'] = $validated['appliesToPt'];
        }

        if (isset($validated['appliesToMp'])) {
            $mapped['applies_to_mp'] = $validated['appliesToMp'];
        }

        if (isset($validated['appliesToCompounds'])) {
            $mapped['applies_to_compounds'] = $validated['appliesToCompounds'];
        }

        return $mapped;
    }
}
