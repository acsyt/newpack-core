<?php

namespace App\Http\Requests\ProductSubclass;

use Illuminate\Foundation\Http\FormRequest;

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
}
