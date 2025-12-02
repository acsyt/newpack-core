<?php

namespace App\Enums;

class ProductType
{
    const PRODUCT_TYPE_FINISHED = 'PT';
    const PRODUCT_TYPE_RAW = 'MP'; // Materia Prima
    const PRODUCT_TYPE_SERVICE = 'SERV'; // Servicio
    const PRODUCT_TYPE_COMPOUND = 'COMP'; // Compuesto
    const PRODUCT_TYPE_REFACEMENT = 'REF'; // Refacciones

    public static function humanReadableType($type): string {
        return match ($type) {
            self::PRODUCT_TYPE_FINISHED => 'Producto Terminado',
            self::PRODUCT_TYPE_RAW => 'Materia Prima',
            self::PRODUCT_TYPE_SERVICE => 'Servicio',
            self::PRODUCT_TYPE_COMPOUND => 'Compuesto',
            self::PRODUCT_TYPE_REFACEMENT => 'Refacciones',
        };
    }
}