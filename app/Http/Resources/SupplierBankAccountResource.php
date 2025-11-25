<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *   schema="SupplierBankAccountResource",
 *   title="SupplierBankAccount",
 *   description="Supplier bank account resource",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="supplierId", type="integer", example=1),
 *   @OA\Property(property="bankName", type="string", example="BBVA"),
 *   @OA\Property(property="accountNumber", type="string", nullable=true, example="0123456789"),
 *   @OA\Property(property="clabe", type="string", nullable=true, example="012180001234567890"),
 *   @OA\Property(property="swiftCode", type="string", nullable=true, example="BBVAMXMMXXX"),
 *   @OA\Property(property="currency", type="string", example="MXN"),
 *   @OA\Property(property="isPrimary", type="boolean", example=true),
 *   @OA\Property(property="status", type="string", enum={"active","inactive"}, example="active"),
 *   @OA\Property(property="notes", type="string", nullable=true, example="Cuenta principal"),
 *   @OA\Property(property="createdAt", type="string", format="date-time"),
 *   @OA\Property(property="updatedAt", type="string", format="date-time"),
 *   @OA\Property(property="deletedAt", type="string", format="date-time", nullable=true)
 * )
 */
class SupplierBankAccountResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'            => $this->id,
            'supplierId'    => $this->supplier_id,
            'bankName'      => $this->bank_name,
            'accountNumber' => $this->account_number,
            'clabe'         => $this->clabe,
            'swiftCode'     => $this->swift_code,
            'currency'      => $this->currency,
            'isPrimary'     => $this->is_primary,
            'status'        => $this->status,
            'notes'         => $this->notes,
            'createdAt'     => $this->created_at,
            'updatedAt'     => $this->updated_at,
            'deletedAt'     => $this->deleted_at,
        ];
    }
}
