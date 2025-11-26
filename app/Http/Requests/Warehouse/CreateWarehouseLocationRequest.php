<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   schema="CreateWarehouseLocationRequest",
 *   title="Create Warehouse Location Request",
 *   description="Request body for creating a warehouse location",
 *   required={"warehouse_id"},
 *   @OA\Property(property="warehouse_id", type="integer", example=1),
 *   @OA\Property(property="aisle", type="string", nullable=true, example="A-01"),
 *   @OA\Property(property="shelf", type="string", nullable=true, example="S-02"),
 *   @OA\Property(property="section", type="string", nullable=true, example="L-03"),
 * )
 */
class CreateWarehouseLocationRequest extends FormRequest
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
            "aisle" =>              ["nullable", "string", 'max:50',],
            "shelf" =>              ["nullable", "string", 'max:50',],
            "section" =>            ["nullable", "string", 'max:50',],
            "warehouse_id" =>       ['required','exists:warehouses,id'],
        ];
    }
}
