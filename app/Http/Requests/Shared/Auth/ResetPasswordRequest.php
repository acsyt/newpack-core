<?php

namespace App\Http\Requests\Shared\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Shared\AuthService;

class ResetPasswordRequest extends FormRequest {
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
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed'
            ],
            'password_confirmation' => [
                'required',
                'string'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                   => 'El correo electrónico es obligatorio.',
            'email.email'                      => 'El correo electrónico debe tener un formato válido.',
            'email.max'                        => 'El correo electrónico no puede tener más de 255 caracteres.',
            'token.required'                   => 'El token es obligatorio.',
            'token.min'                        => 'El token debe tener al menos 32 caracteres.',
            'password.required'                => 'La contraseña es obligatoria.',
            'password.min'                     => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'                => 'La confirmación de contraseña no coincide.',
            'password_confirmation.required'    => 'La confirmación de contraseña es obligatoria.',
        ];
    }
}
