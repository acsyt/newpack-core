<?php

namespace App\Http\Requests\Shared\Auth;

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
            'regex:/^(?:\+[1-9]\d{7,14}|[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,})$/i'],
            'password' => ['required', 'string', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array {
        return [
            'username.required' => 'El correo electrónico es obligatorio.',
            'username.email'    => 'El correo electrónico debe tener un formato válido.',
            'username.max'      => 'El correo electrónico no puede tener más de 255 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }
}
