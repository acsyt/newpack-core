<?php

namespace App\Http\Requests\Product;

use App\Enums\ProductType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="StoreProductRequest",
 *     type="object",
 *     required={"name", "sku", "type", "unit_of_measure"},
 *     @OA\Property(property="name", type="string", example="Polietileno de Baja Densidad"),
 *     @OA\Property(property="sku", type="string", example="MP-PEBD-001"),
 *     @OA\Property(property="type", type="string", enum={"raw_material", "compound", "ingredient", "service", "wip"}, example="raw_material"),
 *     @OA\Property(property="unit_of_measure", type="string", example="kg"),
 *     @OA\Property(property="average_cost", type="number", format="float", nullable=true, example=25.50),
 *     @OA\Property(property="last_purchase_price", type="number", format="float", nullable=true, example=30.00),
 *     @OA\Property(property="current_stock", type="number", format="float", nullable=true, example=1000.00),
 *     @OA\Property(property="min_stock", type="number", format="float", nullable=true, example=100.00),
 *     @OA\Property(property="max_stock", type="number", format="float", nullable=true, example=5000.00),
 *     @OA\Property(property="track_batches", type="boolean", nullable=true, example=true),
 *     @OA\Property(property="is_active", type="boolean", nullable=true, example=true),
 *     @OA\Property(property="is_sellable", type="boolean", nullable=true, example=false),
 *     @OA\Property(property="is_purchasable", type="boolean", nullable=true, example=true),
 *     @OA\Property(
 *         property="ingredients",
 *         type="array",
 *         description="Required if type is 'compuesto'",
 *         @OA\Items(
 *             type="object",
 *             required={"ingredient_id", "quantity"},
 *             @OA\Property(property="ingredient_id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="number", format="float", example=0.5),
 *             @OA\Property(property="wastage_percent", type="number", format="float", nullable=true, example=2.0),
 *             @OA\Property(property="process_stage", type="string", nullable=true, example="EXTRUSION"),
 *             @OA\Property(property="is_active", type="boolean", nullable=true, example=true)
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
        $rules = [
            'name'                  => ['required', 'string', 'max:255'],
            'sku'                   => ['required', 'string', 'max:255', 'unique:products,sku'],
            'type'                  => ['required', Rule::enum(ProductType::class)],
            'unit_of_measure'       => ['required', 'string', 'max:10'],
            'average_cost'          => ['nullable', 'numeric', 'min:0'],
            'last_purchase_price'   => ['nullable', 'numeric', 'min:0'],
            'current_stock'         => ['nullable', 'numeric', 'min:0'],
            'min_stock'             => ['nullable', 'numeric', 'min:0'],
            'max_stock'             => ['nullable', 'numeric', 'min:0'],
            'track_batches'         => ['nullable', 'boolean'],
            'is_active'             => ['nullable', 'boolean'],
            'is_sellable'           => ['nullable', 'boolean'],
            'is_purchasable'        => ['nullable', 'boolean'],
        ];

        if ($this->input('type') === ProductType::COMPOUND->value) {
            $rules['ingredients']               = ['required', 'array', 'min:1'];
            $rules['ingredients.*.ingredient_id'] = ['required', 'integer', 'exists:products,id'];
            $rules['ingredients.*.quantity']      = ['required', 'numeric', 'min:0.0001'];
            $rules['ingredients.*.wastage_percent']  = ['nullable', 'numeric', 'min:0', 'max:100'];
            $rules['ingredients.*.process_stage']  = ['nullable', 'string', 'max:50'];
            $rules['ingredients.*.is_active']     = ['nullable', 'boolean'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required'                         => 'El nombre del producto es obligatorio',
            'sku.required'                          => 'El SKU es obligatorio',
            'sku.unique'                            => 'Este SKU ya está registrado',
            'type.required'                         => 'El tipo de producto es obligatorio',
            'type.in'                               => 'El tipo de producto no es válido',
            'unit_of_measure.required'              => 'La unidad de medida es obligatoria',
            'ingredients.required'                  => 'Los ingredientes son obligatorios para productos compuestos',
            'ingredients.min'                       => 'Debe especificar al menos un ingrediente',
            'ingredients.*.ingredient_id.required'  => 'El ID del ingrediente es obligatorio',
            'ingredients.*.ingredient_id.exists'    => 'El ingrediente especificado no existe',
            'ingredients.*.quantity.required'       => 'La cantidad del ingrediente es obligatoria',
            'ingredients.*.quantity.min'            => 'La cantidad debe ser mayor a 0',
            'ingredients.*.wastage_percent.min'     => 'El porcentaje de desperdicio debe ser mayor o igual a 0',
            'ingredients.*.wastage_percent.max'     => 'El porcentaje de desperdicio debe ser menor o igual a 100',
            'ingredients.*.process_stage.max'       => 'El nombre del proceso debe tener un máximo de 50 caracteres',
            'ingredients.*.is_active.boolean'       => 'El estado del ingrediente debe ser un booleano',
        ];
    }
}
