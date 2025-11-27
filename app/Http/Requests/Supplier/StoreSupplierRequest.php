<?php

namespace App\Http\Requests\Supplier;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="StoreSupplierRequest",
 *     type="object",
 *     required={"company_name"},
 *     @OA\Property(property="company_name", type="string", example="Proveedora SA de CV"),
 *     @OA\Property(property="contact_name", type="string", nullable=true, example="Juan Pérez"),
 *     @OA\Property(property="email", type="string", format="email", nullable=true, example="contacto@proveedora.com"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="5512345678"),
 *     @OA\Property(property="phone_secondary", type="string", nullable=true, example="5587654321"),
 *     @OA\Property(property="suburb_id", type="integer", nullable=true, example=1),
 *     @OA\Property(property="street", type="string", nullable=true, example="Av. Industrial"),
 *     @OA\Property(property="exterior_number", type="string", nullable=true, example="500"),
 *     @OA\Property(property="interior_number", type="string", nullable=true, example="A"),
 *     @OA\Property(property="address_reference", type="string", nullable=true, example="Zona industrial norte"),
 *     @OA\Property(property="rfc", type="string", nullable=true, example="PRO850101AB2"),
 *     @OA\Property(property="legal_name", type="string", nullable=true, example="Proveedora SA de CV"),
 *     @OA\Property(property="tax_system", type="string", nullable=true, example="601"),
 *     @OA\Property(property="use_cfdi", type="string", nullable=true, example="G03"),
 *     @OA\Property(property="supplier_type", type="string", enum={"product", "service", "both"}, example="product"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "suspended", "blacklisted"}, example="active"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Proveedor confiable")
 * )
 */
class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:suppliers,email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'phone_secondary' => ['nullable', 'string', 'max:20'],
            'suburb_id' => ['nullable', 'integer', 'exists:suburbs,id'],
            'street' => ['nullable', 'string', 'max:255'],
            'exterior_number' => ['nullable', 'string', 'max:20'],
            'interior_number' => ['nullable', 'string', 'max:20'],
            'address_reference' => ['nullable', 'string'],
            'rfc' => ['nullable', 'string', 'max:13', 'unique:suppliers,rfc', 'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'tax_system' => ['nullable', 'string', 'max:10'],
            'use_cfdi' => ['nullable', 'string', 'max:10'],
            'supplier_type' => ['nullable', 'string', 'in:product,service,both'],
            'status' => ['nullable', 'string', 'in:active,inactive,suspended,blacklisted'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'El nombre de la empresa es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Este email ya está registrado',
            'rfc.regex' => 'El RFC no tiene un formato válido',
            'rfc.unique' => 'Este RFC ya está registrado',
            'suburb_id.exists' => 'La colonia seleccionada no existe',
            'supplier_type.in' => 'El tipo de proveedor debe ser product, service o both',
        ];
    }
}
