<?php

namespace App\Classes\Sat;

class Exportacion
{
    const NOAPLICA = '01';
    const DEFINITIVA = '02';
    const TEMPORAL = '03';

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

    private static function getVars()
    {
        return [
            self::NOAPLICA => 'No Aplica',
            self::DEFINITIVA => 'Definitiva',
            self::TEMPORAL => 'Temporal'
        ];
    }
}
