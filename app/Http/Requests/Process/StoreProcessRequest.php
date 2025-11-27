<?php

namespace App\Http\Requests\Process;

use Illuminate\Foundation\Http\FormRequest;

class StoreProcessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:processes,code'],
            'name' => ['required', 'string', 'max:255'],
            'appliesToPt' => ['nullable', 'boolean'],
            'appliesToMp' => ['nullable', 'boolean'],
            'appliesToCompounds' => ['nullable', 'boolean'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();

        return [
            'code' => $validated['code'],
            'name' => $validated['name'],
            'applies_to_pt' => $validated['appliesToPt'] ?? false,
            'applies_to_mp' => $validated['appliesToMp'] ?? false,
            'applies_to_compounds' => $validated['appliesToCompounds'] ?? false,
        ];
    }
}
