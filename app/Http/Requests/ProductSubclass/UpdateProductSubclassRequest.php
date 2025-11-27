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
            'product_class_id' => ['sometimes', 'integer', 'exists:product_classes,id'],
            'description' => ['sometimes', 'nullable', 'string'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255', Rule::unique('product_subclasses', 'slug')->ignore($subclassId)],
        ];
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
