<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'transfer_id' => ['required', 'exists:transfers,id'],
            'receiving_notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.transfer_item_id' => ['required', 'exists:transfer_items,id'],
            'items.*.quantity_received' => ['required', 'numeric', 'min:0'],
            'items.*.quantity_missing' => ['nullable', 'numeric', 'min:0'],
            'items.*.quantity_damaged' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'transfer_id' => 'transferencia',
            'receiving_notes' => 'notas de recepción',
            'items' => 'items',
            'items.*.transfer_item_id' => 'item de transferencia',
            'items.*.quantity_received' => 'cantidad recibida',
        ];
    }
}
