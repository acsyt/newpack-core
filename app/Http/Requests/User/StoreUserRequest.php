<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 * schema="StoreUserRequest",
 * required={"name", "last_name", "email"},
 * @OA\Property(property="name", type="string", example="Juan"),
 * @OA\Property(property="last_name", type="string", example="Pérez"),
 * @OA\Property(property="email", type="string", format="email", example="juan@mail.com"),
 * )
*/
class StoreUserRequest extends FormRequest
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
            'name'              => ['required', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'unique:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'The name is required',
            'name.string'               => 'The name must be a string',
            'name.max'                  => 'The name must be less than 255 characters',
            'last_name.required'        => 'The last name is required',
            'last_name.string'          => 'The last name must be a string',
            'last_name.max'             => 'The last name must be less than 255 characters',
            'email.required'            => 'The email is required',
            'email.email'               => 'The email must be a valid email address',
            'email.unique'              => 'The email is already registered',
        ];
    }
}
