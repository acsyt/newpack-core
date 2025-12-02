<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 * schema="UpdateCurrencyRequest",
 * @OA\Property(property="name", type="string", example="US Dollar"),
 * @OA\Property(property="code", type="string", example="USD"),
 * @OA\Property(property="active", type="boolean", example=true)
 * )
 */
class UpdateCurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $currencyId = $this->route('currency');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:10',
                Rule::unique('currencies')->ignore($currencyId)
            ],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The currency name is required',
            'name.string' => 'The currency name must be a string',
            'name.max' => 'The currency name must be less than 255 characters',
            'code.required' => 'The currency code is required',
            'code.string' => 'The currency code must be a string',
            'code.max' => 'The currency code must be less than 10 characters',
            'code.unique' => 'The currency code is already registered',
            'active.boolean' => 'The active status must be a boolean',
        ];
    }
}
