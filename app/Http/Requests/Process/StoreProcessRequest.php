<?php

namespace App\Http\Requests\Process;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="StoreProcessRequest",
 *     type="object",
 *     required={"code", "name"},
 *     @OA\Property(property="code", type="string", example="PROC-001", description="Unique process code"),
 *     @OA\Property(property="name", type="string", example="Molding", description="Process name"),
 *     @OA\Property(property="appliesToPt", type="boolean", example=true, description="Applies to PT (Producto Terminado)"),
 *     @OA\Property(property="appliesToMp", type="boolean", example=false, description="Applies to MP (Materia Prima)"),
 *     @OA\Property(property="appliesToCompounds", type="boolean", example=true, description="Applies to Compounds")
 * )
 */
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

    public function messages(): array
    {
        return [
            'code.required' => 'The process code is required.',
            'code.unique' => 'A process with this code already exists.',
            'name.required' => 'The process name is required.',
        ];
    }
}
