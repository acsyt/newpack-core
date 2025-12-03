<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 * schema="UpdateUserPasswordRequest",
 * required={"password", "password_confirmation"},
 * @OA\Property(property="password", type="string", format="password", example="newsecret123"),
 * @OA\Property(property="password_confirmation", type="string", format="password", example="newsecret123")
 * )
 */
class UpdateUserPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.change-password');
    }

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'La contraseña es requerida',
            'password.string'   => 'La contraseña debe ser una cadena',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'La contraseña de confirmación no coincide',
        ];
    }
}
