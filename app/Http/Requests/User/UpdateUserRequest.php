<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 * schema="UpdateUserRequest",
 * @OA\Property(property="name", type="string", example="Juan"),
 * @OA\Property(property="last_name", type="string", example="Pérez"),
 * @OA\Property(property="email", type="string", format="email", example="juan@mail.com"),
 * @OA\Property(property="active", type="boolean", example=true)
 * )
*/
class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name'      => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email'     => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name is required',
            'name.string' => 'The name must be a string',
            'name.max' => 'The name must be less than 255 characters',
            'last_name.required' => 'The last name is required',
            'last_name.string' => 'The last name must be a string',
            'last_name.max' => 'The last name must be less than 255 characters',
            'email.required' => 'The email is required',
            'email.email' => 'The email must be a valid email address',
            'email.unique' => 'The email is already registered',
            'active.boolean' => 'The active status must be a boolean',
        ];
    }
}
