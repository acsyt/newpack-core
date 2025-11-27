<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreProductRequest",
 *     type="object",
 *     required={"name", "sku", "type", "measure_unit_id"},
 *     @OA\Property(property="name", type="string", example="Polietileno de Baja Densidad"),
 *     @OA\Property(property="sku", type="string", example="MP-PEBD-001"),
 *     @OA\Property(property="type", type="string", enum={"raw_material", "compound", "ingredient", "service", "wip"}, example="raw_material"),
 *     @OA\Property(property="measure_unit_id", type="integer", example=1),
 *     @OA\Property(property="average_cost", type="number", format="float", nullable=true, example=25.50),
 *     @OA\Property(property="last_purchase_price", type="number", format="float", nullable=true, example=24.00),
 *     @OA\Property(property="current_stock", type="number", format="float", nullable=true, example=100.00),
 *     @OA\Property(property="min_stock", type="number", format="float", nullable=true, example=10.00),
 *     @OA\Property(property="max_stock", type="number", format="float", nullable=true, example=500.00),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="is_sellable", type="boolean", example=false),
 *     @OA\Property(property="is_purchasable", type="boolean", example=true),
 *     @OA\Property(
 *         property="ingredients",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"ingredient_id", "quantity"},
 *             @OA\Property(property="ingredient_id", type="integer", example=5),
 *             @OA\Property(property="quantity", type="number", format="float", example=1.5),
 *             @OA\Property(property="wastage_percent", type="number", format="float", example=0.05),
 *             @OA\Property(property="process_stage", type="string", example="EXTRUSION")
 *         )
 *     )
 * )
 */
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'sku'                   => ['required', 'string', 'max:100', 'unique:products,sku'],
            'type'                  => ['required', Rule::enum(ProductType::class)],
            'measure_unit_id'       => ['required', 'integer', 'exists:measure_units,id'],
            'average_cost'          => ['nullable', 'numeric', 'min:0'],
            'min_stock'             => ['nullable', 'numeric', 'min:0'],
            'max_stock'             => ['nullable', 'numeric', 'min:0'],
            'is_active'             => ['boolean'],
            // Validación para ingredientes (solo si es compuesto)
            'ingredients'                   => ['nullable', 'array'],
            'ingredients.*.ingredient_id'   => ['required_with:ingredients', 'integer', 'exists:products,id'],
            'ingredients.*.quantity'        => ['required_with:ingredients', 'numeric', 'min:0.0001'],
            'ingredients.*.wastage_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ingredients.*.process_stage'   => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                         => 'El nombre del producto es obligatorio',
            'sku.required'                          => 'El SKU es obligatorio',
            'sku.unique'                            => 'El SKU ya está registrado',
            'type.required'                         => 'El tipo de producto es obligatorio',
            'type.in'                               => 'El tipo de producto no es válido',
            'measure_unit_id.required'              => 'La unidad de medida es obligatoria',
            'measure_unit_id.exists'                => 'La unidad de medida seleccionada no es válida',
            'ingredients.required'                  => 'Los ingredientes son obligatorios para productos compuestos',
            'ingredients.min'                       => 'Debe especificar al menos un ingrediente',
            'ingredients.*.ingredient_id.required_with'  => 'El ID del ingrediente es obligatorio',
            'ingredients.*.ingredient_id.exists'    => 'El ingrediente especificado no existe',
            'ingredients.*.quantity.required_with'       => 'La cantidad del ingrediente es obligatoria',
            'ingredients.*.quantity.min'            => 'La cantidad debe ser mayor a 0',
            'ingredients.*.wastage_percent.min'     => 'El porcentaje de desperdicio debe ser mayor o igual a 0',
            'ingredients.*.wastage_percent.max'     => 'El porcentaje de desperdicio debe ser menor o igual a 100',
            'ingredients.*.process_stage.max'       => 'El nombre del proceso debe tener un máximo de 50 caracteres',
            'ingredients.*.is_active.boolean'       => 'El estado del ingrediente debe ser un booleano',
        ];
    }
}
