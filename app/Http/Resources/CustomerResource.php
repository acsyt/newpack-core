<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="CustomerResource",
 *   title="Customer",
 *   description="Customer resource with complete information",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Juan"),
 *   @OA\Property(property="lastName", type="string", example="Pérez"),
 *   @OA\Property(property="fullName", type="string", example="Juan Pérez"),
 *   @OA\Property(property="email", type="string", format="email", nullable=true, example="juan.perez@example.com"),
 *   @OA\Property(property="emailVerifiedAt", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="phone", type="string", nullable=true, example="5512345678"),
 *   @OA\Property(property="phoneSecondary", type="string", nullable=true, example="5587654321"),
 *   @OA\Property(property="suburbId", type="integer", nullable=true, example=1),
 *   @OA\Property(property="street", type="string", nullable=true, example="Av. Insurgentes"),
 *   @OA\Property(property="exteriorNumber", type="string", nullable=true, example="123"),
 *   @OA\Property(property="interiorNumber", type="string", nullable=true, example="4B"),
 *   @OA\Property(property="addressReference", type="string", nullable=true, example="Entre calles X y Y"),
 *   @OA\Property(property="fullAddress", type="string", nullable=true, example="Av. Insurgentes 123, Int. 4B, Col. Centro, CDMX"),
 *   @OA\Property(property="rfc", type="string", nullable=true, example="PEPJ800101AB1"),
 *   @OA\Property(property="razonSocial", type="string", nullable=true, example="Empresa SA de CV"),
 *   @OA\Property(property="regimenFiscal", type="string", nullable=true, example="601"),
 *   @OA\Property(property="usoCfdi", type="string", nullable=true, example="G03"),
 *   @OA\Property(property="status", type="string", enum={"active","inactive","suspended","blacklisted"}, example="active"),
 *   @OA\Property(property="clientType", type="string", enum={"individual","company"}, example="individual"),
 *   @OA\Property(property="notes", type="string", nullable=true, example="Cliente VIP"),
 *   @OA\Property(property="createdBy", type="integer", nullable=true, example=1),
 *   @OA\Property(property="updatedBy", type="integer", nullable=true, example=1),
 *   @OA\Property(property="deletedBy", type="integer", nullable=true, example=null),
 *   @OA\Property(property="createdAt", type="string", format="date-time"),
 *   @OA\Property(property="updatedAt", type="string", format="date-time"),
 *   @OA\Property(property="deletedAt", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="suburb", ref="#/components/schemas/SuburbResource", nullable=true),
 * )
 */
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'lastName'          => $this->last_name,
            'fullName'          => $this->full_name,

            'email'             => $this->email,
            'emailVerifiedAt'    => $this->email_verified_at,
            'phone'             => $this->phone,
            'phoneSecondary'    => $this->phone_secondary,

            'suburbId'          => $this->suburb_id,
            'street'            => $this->street,
            'exteriorNumber'    => $this->exterior_number,
            'interiorNumber'    => $this->interior_number,
            'addressReference'  => $this->address_reference,
            'fullAddress'       => $this->full_address,

            'rfc'               => $this->rfc,
            'legalName'         => $this->legal_name,
            'taxSystem'         => $this->tax_system,
            'cfdiUse'           => $this->cfdi_use,

            'status'            => $this->status,
            'clientType'        => $this->client_type,

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
