<?php

namespace App\Enums;

enum ProductType: string
{
    case FINISHED = 'PT';
    case RAW = 'MP';
    case SERVICE = 'SERV';
    case COMPOUND = 'COMP';
    case REFACEMENT = 'REF';

    public function humanReadable(): string
    {
        return match ($this) {
            self::FINISHED => 'Producto Terminado',
            self::RAW => 'Materia Prima',
            self::SERVICE => 'Servicio',
            self::COMPOUND => 'Compuesto',
            self::REFACEMENT => 'Refacciones',
        };
    }
}