<?php

namespace App\Http\Requests\Central\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
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
            'code'              => ['required', 'string', 'unique:tenants,code', 'max:100'],
            'domain'            => ['required', 'string', 'max:255', 'unique:domains,domain'],
        ];
    }

    public function messages(): array {
        return [
            'name.required'     => 'El nombre del tenant es obligatorio. Por favor, proporciona un nombre.',
            'name.string'       => 'El nombre del tenant debe ser un texto válido.',
            'name.max'          => 'El nombre del tenant no debe exceder los 255 caracteres.',

            'domain.required'   => 'El dominio del tenant es obligatorio. Por favor, proporciona un dominio válido.',
            'domain.string'     => 'El dominio debe ser una cadena de texto válida.',
            'domain.max'        => 'El dominio no debe exceder los 255 caracteres.',
            'domain.unique'     => 'Este dominio ya está en uso. Por favor, elige un dominio diferente.',

            'code.required'     => 'El código del tenant es obligatorio. Es un identificador único.',
            'code.string'       => 'El código del tenant debe ser una cadena de texto válida.',
            'code.unique'       => 'Este código ya está en uso. Por favor, elige un código diferente.',
            'code.max'          => 'El código del tenant no debe exceder los 100 caracteres.',
        ];
    }
}
