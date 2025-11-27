<?php

namespace App\Http\Requests\ProductClass;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="StoreProductClassRequest",
 *     type="object",
 *     required={"code", "name"},
 *     @OA\Property(property="code", type="string", example="CL-001", description="Unique class code"),
 *     @OA\Property(property="name", type="string", example="Plastics", description="Class name"),
 *     @OA\Property(property="description", type="string", example="Plastic materials", description="Class description"),
 *     @OA\Property(property="slug", type="string", example="plastics", description="URL-friendly slug")
 * )
 */
class StoreProductClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:product_classes,code'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_classes,slug'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();

        return [
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'slug' => $validated['slug'] ?? \Str::slug($validated['name']),
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The class code is required.',
            'code.unique' => 'A class with this code already exists.',
            'name.required' => 'The class name is required.',
            'slug.unique' => 'A class with this slug already exists.',
        ];
    }
}
