<?php

namespace App\Classes\Sat;

class MetodoPago
{
    const PAGO_UNICA_EXHIBICION = 'PUE';
    const PAGO_PARCIALIDADES = 'PPD';

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
            self::PAGO_UNICA_EXHIBICION => 'Pago en una sola exhibición',
            self::PAGO_PARCIALIDADES => 'Pago en parcialidades o diferido',
        ];
    }
}
