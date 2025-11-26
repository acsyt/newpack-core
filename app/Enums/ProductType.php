<?php

namespace App\Enums;

enum ProductType: string
{
    case RAW_MATERIAL = 'MP'; // Materia Prima
    case FINISHED_PRODUCT = 'PT'; // Producto Terminado
    case SERVICE = 'SERV'; // Servicio
    case SPARE_PART = 'REF'; // Refacciones
    case COMPOUND = 'COMP'; // Compuesto (Keeping it just in case, or maybe PT is enough)
    // case SUPPLY = 'supply'; // Removed as not in JSON
    // case WIP = 'wip'; // Removed as not in JSON

    public function label(): string
    {
        return match($this) {
            self::RAW_MATERIAL => 'Materia Prima',
            self::FINISHED_PRODUCT => 'Producto Terminado',
            self::SERVICE => 'Servicio',
            self::SPARE_PART => 'Refacciones',
            self::COMPOUND => 'Compuesto',
        };
    }
}
