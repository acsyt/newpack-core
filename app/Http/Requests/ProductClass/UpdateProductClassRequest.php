<?php

namespace App\Http\Requests\ProductClass;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
}
