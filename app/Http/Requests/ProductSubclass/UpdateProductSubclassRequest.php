<?php

namespace App\Http\Requests\ProductSubclass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="UpdateProductSubclassRequest",
 *     type="object",
 *     @OA\Property(property="code", type="string", example="SC-001", description="Unique subclass code"),
 *     @OA\Property(property="name", type="string", example="HDPE", description="Subclass name"),
 *     @OA\Property(property="productClassId", type="integer", example=1, description="Product class ID"),
 *     @OA\Property(property="description", type="string", example="High-density polyethylene", description="Subclass description"),
 *     @OA\Property(property="slug", type="string", example="hdpe", description="URL-friendly slug")
 * )
 */
class UpdateProductSubclassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subclassId = $this->route('productSubclass');

        return [
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('product_subclasses', 'code')->ignore($subclassId)],
            'name' => ['sometimes', 'string', 'max:255'],
            'productClassId' => ['sometimes', 'integer', 'exists:product_classes,id'],
            'description' => ['sometimes', 'nullable', 'string'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('product_subclasses', 'slug')->ignore($subclassId)],
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

        if (isset($validated['productClassId'])) {
            $mapped['product_class_id'] = $validated['productClassId'];
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
            'code.unique' => 'A subclass with this code already exists.',
            'productClassId.exists' => 'The selected product class does not exist.',
            'slug.unique' => 'A subclass with this slug already exists.',
        ];
    }
}
