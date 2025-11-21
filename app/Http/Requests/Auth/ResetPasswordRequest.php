<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

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
            'email.required'                   => 'The email field is required.',
            'email.email'                      => 'The email must be a valid email address.',
            'email.max'                        => 'The email may not be greater than 255 characters.',
            'token.required'                   => 'The token is required.',
            'token.min'                        => 'The token must be at least 32 characters.',
            'password.required'                => 'The password is required.',
            'password.min'                     => 'The password must be at least 8 characters.',
            'password.confirmed'                => 'The password confirmation does not match.',
            'password_confirmation.required'    => 'The password confirmation is required.',
        ];
    }
}
