<?php

namespace App\Classes\Sat;

class ObjetoImpuesto
{
    const NO_OBJETO_DE_IMPUESTOS = '01';

    const SI_OBJETO_DE_IMPUESTOS = '02';

    const EXENTO = '03';

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
            self::NO_OBJETO_DE_IMPUESTOS => 'No objeto de impuesto',
            self::SI_OBJETO_DE_IMPUESTOS => 'Sí objeto de impuesto',
            self::EXENTO => 'Exento'
        ];
    }
}
