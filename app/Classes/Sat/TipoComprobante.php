<?php

namespace App\Classes\Sat;

class TipoComprobante
{
    const INGRESO = 'I';
    const EGRESO = 'E';
    const TRASLADO = 'T';
    const NOMINA = 'N';
    const PAGO = 'P';

    /**
     * Obtiene el nombre o label a partir del valor dado
     *
     * @param string $value
     * @return string
     */
    public static function getName($value)
    {
        $consts = self::getVars();
        if (array_key_exists($value, $consts)) {
            return $consts[$value];
        }

        return false;
    }

    public static function getVars()
    {
        return [
            self::INGRESO => 'Ingreso',
            self::EGRESO => 'Egreso',
            self::TRASLADO => 'Traslado',
            self::NOMINA => 'Nómina',
            self::PAGO => 'Pago'
        ];
    }
}
