<?php

namespace App\Http\Requests\User;

use App\Http\Traits\HasPhoneValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 * schema="UpdateUserRequest",
 * @OA\Property(property="name", type="string", example="Juan"),
 * @OA\Property(property="last_name", type="string", example="Pérez"),
 * @OA\Property(property="email", type="string", format="email", example="juan@mail.com"),
 * @OA\Property(property="phone", type="string", example="+525512345678"),
 * @OA\Property(property="immediate_supervisor_id", type="integer", example=1),
 * @OA\Property(property="password", type="string", format="password", example="secret123"),
 * @OA\Property(property="password_confirmation", type="string", format="password", example="secret123"),
 * @OA\Property(property="role_id", type="integer", example=2),
 * @OA\Property(property="active", type="boolean", example=true)
 * )
*/
class UpdateUserRequest extends FormRequest
{
    use HasPhoneValidation;
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
            'active'                    => ['sometimes', 'boolean'],
            'phone'                     => ['sometimes', 'nullable', 'phone:' . implode(',', self::SUPPORTED_REGIONS)],
            'immediate_supervisor_id'   => ['sometimes', 'nullable', 'exists:users,id', Rule::notIn([$userId])],
            'password'                  => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'role_id'                   => ['sometimes', 'exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es requerido',
            'name.string' => 'El nombre debe ser una cadena',
            'name.max' => 'El nombre debe tener menos de 255 caracteres',
            'last_name.required' => 'El apellido es requerido',
            'last_name.string' => 'El apellido debe ser una cadena',
            'last_name.max' => 'El apellido debe tener menos de 255 caracteres',
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico debe ser una dirección válida',
            'email.unique' => 'El correo electrónico ya está registrado',
            'active.boolean' => 'El estado activo debe ser un booleano',
            'phone.phone'               => 'El número de teléfono no es válido. Use un número válido de México o EE. UU.',
            'immediate_supervisor_id.exists' => 'El supervisor seleccionado no es válido',
            'immediate_supervisor_id.not_in' => 'El usuario no puede ser su propio supervisor',
            'password.string' => 'La contraseña debe ser una cadena',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'role_id.exists' => 'El rol seleccionado no es válido',
        ];
    }
}
