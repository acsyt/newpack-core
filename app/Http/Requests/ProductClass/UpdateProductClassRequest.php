<?php

namespace App\Http\Requests\ProductClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="UpdateProductClassRequest",
 *     type="object",
 *     @OA\Property(property="code", type="string", example="CL-001", description="Unique class code"),
 *     @OA\Property(property="name", type="string", example="Plastics", description="Class name"),
 *     @OA\Property(property="description", type="string", example="Plastic materials", description="Class description"),
 *     @OA\Property(property="slug", type="string", example="plastics", description="URL-friendly slug")
 * )
 */
class UpdateProductClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $classId = $this->route('productClass');

        return [
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('product_classes', 'code')->ignore($classId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('product_classes', 'slug')->ignore($classId)],
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
            if (!isset($validated['slug'])) {
                $mapped['slug'] = \Str::slug($validated['name']);
            }
        }

        if (isset($validated['description'])) {
            $mapped['description'] = $validated['description'];
        }

        if (isset($validated['slug'])) {
            $mapped['slug'] = $validated['slug'];
        }

        return $mapped;
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'A class with this code already exists.',
            'slug.unique' => 'A class with this slug already exists.',
        ];
    }
}
