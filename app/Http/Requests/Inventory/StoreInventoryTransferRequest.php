<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_warehouse_id' => ['required', 'exists:warehouses,id'],
            'destination_warehouse_id' => ['required', 'exists:warehouses,id', 'different:source_warehouse_id'],
            'notes' => ['nullable', 'string'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric', 'min:0.0001'],
            'products.*.source_location_id' => ['nullable', 'exists:warehouse_locations,id'],
            'products.*.destination_location_id' => ['nullable', 'exists:warehouse_locations,id'],
            'products.*.batch_id' => ['nullable', 'exists:batches,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'source_warehouse_id' => 'almacén origen',
            'destination_warehouse_id' => 'almacén destino',
            'products' => 'productos',
            'products.*.product_id' => 'producto',
            'products.*.quantity' => 'cantidad',
            'products.*.source_location_id' => 'ubicación origen',
            'products.*.destination_location_id' => 'ubicación destino',
        ];
    }
}
