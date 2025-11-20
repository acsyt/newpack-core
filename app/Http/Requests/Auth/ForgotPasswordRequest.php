<?php

namespace App\Http\Requests\Shared\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\Shared\AuthService;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'El correo electrónico debe tener un formato válido.',
            'email.max'         => 'El correo electrónico no puede tener más de 255 caracteres.',
        ];
    }
}
