<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case ENTRY = 'entry';
    case EXIT = 'exit';
    case TRANSFER = 'transfer';

    public function label(): string
    {
        return match($this) {
            self::ENTRY => 'Entrada',
            self::EXIT => 'Salida',
            self::TRANSFER => 'Transferencia',
        };
    }
}
