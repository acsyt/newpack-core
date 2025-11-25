<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="UpdateCustomerRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="Juan"),
 *     @OA\Property(property="last_name", type="string", example="Pérez García"),
 *     @OA\Property(property="email", type="string", format="email", nullable=true, example="juan.perez@example.com"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="5512345678"),
 *     @OA\Property(property="phone_secondary", type="string", nullable=true, example="5587654321"),
 *     @OA\Property(property="mobile", type="string", nullable=true, example="5511223344"),
 *     @OA\Property(property="whatsapp", type="string", nullable=true, example="5511223344"),
 *     @OA\Property(property="suburb_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="street", type="string", nullable=true, example="Av. Insurgentes"),
 *     @OA\Property(property="exterior_number", type="string", nullable=true, example="123"),
 *     @OA\Property(property="interior_number", type="string", nullable=true, example="4B"),
 *     @OA\Property(property="address_reference", type="string", nullable=true, example="Entre calles X y Y"),
 *     @OA\Property(property="tax_id", type="string", nullable=true, example="PEGJ800101AB1", description="RFC - Tax ID"),
 *     @OA\Property(property="legal_name", type="string", nullable=true, example="Empresa SA de CV", description="Razón Social"),
 *     @OA\Property(property="tax_system", type="string", nullable=true, example="601", description="Régimen Fiscal"),
 *     @OA\Property(property="cfdi_use", type="string", nullable=true, example="G03", description="Uso CFDI"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended", "blacklisted"}, example="active"),
 *     @OA\Property(property="client_type", type="string", enum={"individual", "company"}, example="individual"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Cliente VIP")
 * )
 */
class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer') ?? $this->route('id');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email,' . $customerId],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'suburb_id' => ['nullable', 'integer', 'exists:suburbs,id'],
            'street' => ['nullable', 'string', 'max:255'],
            'exterior_number' => ['nullable', 'string', 'max:20'],
            'interior_number' => ['nullable', 'string', 'max:20'],
            'address_reference' => ['nullable', 'string'],
            'rfc' => ['nullable', 'string', 'max:13', 'unique:customers,rfc,' . $customerId, 'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tax_system' => ['nullable', 'string', 'max:10'],
            'use_cfdi' => ['nullable', 'string', 'max:10'],
            'status' => ['sometimes', 'string', 'in:active,inactive,suspended,blacklisted'],
            'client_type' => ['sometimes', 'string', 'in:individual,company'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'rfc.regex' => 'El RFC no tiene un formato válido',
            'rfc.unique' => 'Este RFC ya está registrado',
            'suburb_id.exists' => 'La colonia seleccionada no existe',
        ];
    }

}
