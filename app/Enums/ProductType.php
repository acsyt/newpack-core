<?php

namespace App\Enums;

enum ProductType: string
{
    case RAW_MATERIAL = 'raw_material'; // Materia no
    case COMPOUND = 'compound'; // Compuesto
    case SUPPLY = 'supply'; // Insumo
    case SERVICE = 'service'; // Servicio
    case WIP = 'wip'; // Work In Progress

    public function label(): string
    {
        return match($this) {
            self::RAW_MATERIAL => 'Materia Prima',
            self::COMPOUND => 'Compuesto',
            self::SUPPLY => 'Insumo',
            self::SERVICE => 'Servicio',
            self::WIP => 'Work In Progress',
        };
    }
}
