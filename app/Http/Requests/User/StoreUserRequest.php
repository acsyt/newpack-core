<?php

namespace App\Http\Requests\User;

use App\Http\Traits\HasPhoneValidation;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 * schema="StoreUserRequest",
 * required={"name", "last_name", "email"},
 * @OA\Property(property="name", type="string", example="Juan"),
 * @OA\Property(property="last_name", type="string", example="Pérez"),
 * @OA\Property(property="email", type="string", format="email", example="juan@mail.com"),
 * @OA\Property(property="phone", type="string", example="+525512345678"),
 * @OA\Property(property="immediate_supervisor_id", type="integer", example=1),
 * @OA\Property(property="password", type="string", format="password", example="secret123"),
 * @OA\Property(property="password_confirmation", type="string", format="password", example="secret123"),
 * @OA\Property(property="role_id", type="integer", example=2),
 * )
*/
class StoreUserRequest extends FormRequest
{
    use HasPhoneValidation;
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
            'name'              => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:users,email'],
            'phone'             => ['nullable', 'phone:' . implode(',', self::SUPPORTED_REGIONS)],
            'immediate_supervisor_id' => ['nullable', 'exists:users,id'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'role_id'           => ['required', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'El nombre es requerido',
            'name.string'               => 'El nombre debe ser una cadena',
            'name.max'                  => 'El nombre debe tener menos de 255 caracteres',
            'last_name.required'        => 'El apellido es requerido',
            'last_name.string'          => 'El apellido debe ser una cadena',
            'last_name.max'             => 'El apellido debe tener menos de 255 caracteres',
            'email.required'            => 'El correo electrónico es requerido',
            'email.email'               => 'El correo electrónico debe ser una dirección válida',
            'email.unique'              => 'El correo electrónico ya está registrado',
            'phone.phone'               => 'El número de teléfono no es válido. Use un número válido de México o EE. UU.',
            'immediate_supervisor_id.exists' => 'El supervisor seleccionado no es válido',
            'password.required'         => 'La contraseña es requerida',
            'password.string'           => 'La contraseña debe ser una cadena',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres',
            'role_id.required'          => 'El rol es requerido',
            'role_id.exists'            => 'El rol seleccionado no es válido',
        ];
    }
}
