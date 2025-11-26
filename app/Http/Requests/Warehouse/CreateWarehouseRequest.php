<?php

namespace App\Http\Requests\Warehouse;

use Illuminate\Foundation\Http\FormRequest;

use OpenApi\Attributes as OA;

/**
 * @OA\Schema(
 *   schema="CreateWarehouseRequest",
 *   title="Create Warehouse Request",
 *   description="Request body for creating a warehouse",
 *   required={"name", "type", "active"},
 *   @OA\Property(property="name", type="string", example="Almacén Central"),
 *   @OA\Property(property="type", type="string", example="main"),
 *   @OA\Property(property="active", type="boolean", example=true),
 * )
 */
class CreateWarehouseRequest extends FormRequest
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
            "name" =>                            ["required", "string"],
            "type" =>                            ["required", "string"],
            "active" =>                          ["required", "boolean"],
        ];
    }
}
