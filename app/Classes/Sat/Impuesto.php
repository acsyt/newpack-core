<?php

namespace App\Classes\Sat;

class Impuesto
{
    const ISR = '001'; // Impuesto sobre la renta
    const IVA = '002'; // Impuesto al valor agregado
    const IEPS = '003'; // Impuesto especial sobre producción y servicios

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
    public static function onExistent($value)
    {
        $consts = self::getVars();
        return array_key_exists($value, $consts);
    }

    private static function getVars()
    {
        return [
            self::ISR => 'ISR',
            self::IVA => 'IVA',
            self::IEPS => 'IEPS'
        ];
    }
}
