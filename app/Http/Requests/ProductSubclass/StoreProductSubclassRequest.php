<?php

namespace App\Http\Requests\ProductSubclass;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *     schema="StoreProductSubclassRequest",
 *     type="object",
 *     required={"code", "name", "productClassId"},
 *     @OA\Property(property="code", type="string", example="SC-001", description="Unique subclass code"),
 *     @OA\Property(property="name", type="string", example="HDPE", description="Subclass name"),
 *     @OA\Property(property="productClassId", type="integer", example=1, description="Product class ID"),
 *     @OA\Property(property="description", type="string", example="High-density polyethylene", description="Subclass description"),
 *     @OA\Property(property="slug", type="string", example="hdpe", description="URL-friendly slug")
 * )
 */
class StoreProductSubclassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', 'unique:product_subclasses,code'],
            'name' => ['required', 'string', 'max:255'],
            'productClassId' => ['required', 'integer', 'exists:product_classes,id'],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_subclasses,slug'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();

        return [
            'code' => $validated['code'],
            'name' => $validated['name'],
            'product_class_id' => $validated['productClassId'],
            'description' => $validated['description'] ?? null,
            'slug' => $validated['slug'] ?? \Str::slug($validated['name']),
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The subclass code is required.',
            'code.unique' => 'A subclass with this code already exists.',
            'name.required' => 'The subclass name is required.',
            'productClassId.required' => 'The product class is required.',
            'productClassId.exists' => 'The selected product class does not exist.',
            'slug.unique' => 'A subclass with this slug already exists.',
        ];
    }
}
