<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\InventoryMovementType;
use Illuminate\Validation\Rules\Enum;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'warehouse_location_id' => ['nullable', 'exists:warehouse_locations,id'],
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', new Enum(InventoryMovementType::class)],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'batch_id' => ['nullable', 'exists:batches,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
