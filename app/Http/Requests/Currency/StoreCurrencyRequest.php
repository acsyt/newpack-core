<?php

namespace App\Http\Requests\Currency;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 * schema="StoreCurrencyRequest",
 * required={"name", "code"},
 * @OA\Property(property="name", type="string", example="US Dollar"),
 * @OA\Property(property="code", type="string", example="USD"),
 * @OA\Property(property="active", type="boolean", example=true),
 * )
 */
class StoreCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', 'unique:currencies,code'],
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
