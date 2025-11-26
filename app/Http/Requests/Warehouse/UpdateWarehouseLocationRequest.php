<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   schema="UpdateWarehouseLocationRequest",
 *   title="Update Warehouse Location Request",
 *   description="Request body for updating a warehouse location",
 *   required={"id", "warehouse_id"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="warehouse_id", type="integer", example=1),
 *   @OA\Property(property="aisle", type="string", nullable=true, example="A-01"),
 *   @OA\Property(property="shelf", type="string", nullable=true, example="S-02"),
 *   @OA\Property(property="section", type="string", nullable=true, example="L-03"),
 * )
 */
class UpdateWarehouseLocationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' =>                 ['required','exists:warehouse_locations,id'],
            "aisle" =>              ["nullable", "string", 'max:50',],
            "shelf" =>              ["nullable", "string", 'max:50',],
            "section" =>            ["nullable", "string", 'max:50',],
            "warehouse_id" =>       ['required','exists:warehouses,id'],
        ];
    }
}
