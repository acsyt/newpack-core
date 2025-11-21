<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId)
            ],
            'password' => ['sometimes', 'required', 'string', 'min:8'],
            'phone_number' => ['sometimes', 'required', 'string', 'unique:users,phone_number'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name is required',
            'name.string' => 'The name must be a string',
            'name.max' => 'The name must be less than 255 characters',
            'email.required' => 'The email is required',
            'email.email' => 'The email must be a valid email address',
            'email.unique' => 'The email is already registered',
            'password.required' => 'The password is required',
            'password.string' => 'The password must be a string',
            'password.min' => 'The password must be at least 8 characters',
            'is_active.boolean' => 'The active status must be a boolean',
        ];
    }
}
