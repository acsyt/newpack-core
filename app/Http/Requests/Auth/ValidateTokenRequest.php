<?php

namespace App\Http\Requests\Auth;

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
            'email.required' => 'The email field is required.',
            'email.email'    => 'The email must be a valid email address.',
            'token.required' => 'The token is required.',
            'token.min'      => 'The token must be at least 32 characters.',
        ];
    }
}
