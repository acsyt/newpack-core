<?php

namespace App\Http\Requests\Shared\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ValidateTokenRequest extends FormRequest {
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255'
            ],
            'token' => [
                'required',
                'string',
                'min:32',
                'max:255'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'token.required' => 'El token es obligatorio.',
            'token.min' => 'El token debe tener al menos 32 caracteres.',
        ];
    }
}
