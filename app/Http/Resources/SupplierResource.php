<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="SupplierResource",
 *   title="Supplier",
 *   description="Supplier resource with complete information",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="companyName", type="string", example="Proveedora SA de CV"),
 *   @OA\Property(property="contactName", type="string", nullable=true, example="Juan Pérez"),
 *   @OA\Property(property="email", type="string", format="email", nullable=true, example="contacto@proveedora.com"),
 *   @OA\Property(property="phone", type="string", nullable=true, example="5512345678"),
 *   @OA\Property(property="phoneSecondary", type="string", nullable=true, example="5587654321"),
 *   @OA\Property(property="mobile", type="string", nullable=true, example="5511223344"),
 *   @OA\Property(property="whatsapp", type="string", nullable=true, example="5511223344"),
 *   @OA\Property(property="suburbId", type="integer", nullable=true, example=1),
 *   @OA\Property(property="street", type="string", nullable=true, example="Av. Industrial"),
 *   @OA\Property(property="exteriorNumber", type="string", nullable=true, example="500"),
 *   @OA\Property(property="interiorNumber", type="string", nullable=true, example="A"),
 *   @OA\Property(property="addressReference", type="string", nullable=true, example="Zona industrial"),
 *   @OA\Property(property="fullAddress", type="string", nullable=true, example="Av. Industrial 500, Int. A, Col. Centro"),
 *   @OA\Property(property="rfc", type="string", nullable=true, example="PRO850101AB2"),
 *   @OA\Property(property="legalName", type="string", nullable=true, example="Proveedora SA de CV"),
 *   @OA\Property(property="taxSystem", type="string", nullable=true, example="601"),
 *   @OA\Property(property="useCfdi", type="string", nullable=true, example="G03"),
 *   @OA\Property(property="supplierType", type="string", enum={"product","service","both"}, example="product"),
 *   @OA\Property(property="paymentTerms", type="string", nullable=true, example="30 días"),
 *   @OA\Property(property="creditLimit", type="number", format="float", nullable=true, example=100000.00),
 *   @OA\Property(property="status", type="string", enum={"active","inactive","suspended","blacklisted"}, example="active"),
 *   @OA\Property(property="notes", type="string", nullable=true, example="Proveedor confiable"),
 *   @OA\Property(property="createdBy", type="integer", nullable=true, example=1),
 *   @OA\Property(property="updatedBy", type="integer", nullable=true, example=1),
 *   @OA\Property(property="deletedBy", type="integer", nullable=true, example=null),
 *   @OA\Property(property="createdAt", type="string", format="date-time"),
 *   @OA\Property(property="updatedAt", type="string", format="date-time"),
 *   @OA\Property(property="deletedAt", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="suburb", ref="#/components/schemas/SuburbResource", nullable=true),
 * )
 */
class SupplierResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'companyName'       => $this->company_name,
            'contactName'       => $this->contact_name,

            'email'             => $this->email,
            'phone'             => $this->phone,
            'phoneSecondary'    => $this->phone_secondary,
            'mobile'            => $this->mobile,
            'whatsapp'          => $this->whatsapp,

            'suburbId'          => $this->suburb_id,
            'street'            => $this->street,
            'exteriorNumber'    => $this->exterior_number,
            'interiorNumber'    => $this->interior_number,
            'addressReference'  => $this->address_reference,
            'fullAddress'       => $this->full_address,

            'rfc'               => $this->rfc,
            'legalName'         => $this->legal_name,
            'taxSystem'         => $this->tax_system,
            'useCfdi'           => $this->use_cfdi,

            'supplierType'      => $this->supplier_type,
            'paymentTerms'      => $this->payment_terms,
            'creditLimit'       => $this->credit_limit,

            'status'            => $this->status,

            'notes'             => $this->notes,

            'createdBy'         => $this->created_by,
            'updatedBy'         => $this->updated_by,
            'deletedBy'         => $this->deleted_by,

            'createdAt'         => $this->created_at,
            'updatedAt'         => $this->updated_at,
            'deletedAt'         => $this->deleted_at,

            'suburb'            => new SuburbResource($this->whenLoaded('suburb')),
        ];
    }
}
