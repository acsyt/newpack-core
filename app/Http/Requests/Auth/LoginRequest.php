<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'email' => ['required',
                'string',
                'max:255',
                'regex:/^(?:\+[1-9]\d{7,14}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,})$/i'
            ],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array {
        return [
            'email.required' => 'The email field is required.',
            'email.email'    => 'The email must be a valid email address.',
            'email.max'      => 'The email may not be greater than 255 characters.',
            'password.required' => 'The password field is required.',
        ];
    }
}
