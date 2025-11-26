<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   schema="UpdateWarehouseRequest",
 *   title="Update Warehouse Request",
 *   description="Request body for updating a warehouse",
 *   required={"id", "name", "type", "active"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Almacén Central"),
 *   @OA\Property(property="type", type="string", example="main"),
 *   @OA\Property(property="active", type="boolean", example=true),
 * )
 */
class UpdateWarehouseRequest extends FormRequest
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
            "id" =>                              ["required", "integer"],
            "name" =>                            ["required", "string"],
            "type" =>                            ["required", "string"],
            "active" =>                          ["required", "boolean"],
        ];
    }
}
