<?php

namespace App\Classes\Sat;

class FormaPago
{
    const EFECTIVO = '01';
    const CHEQUE_NOMINATIVO = '02';
    const TRANSFERENCIA_ELECTRONICA_DE_FONDOS = '03';
    const TARJETA_DE_CREDITO = '04';
    const MONEDERO_ELECTRONICO = '05';
    const DINERO_ELECTRONICO = '06';
    const VALES_DE_DESPENSA = '08';
    const DACION_EN_PAGO = '12';
    const PAGO_POR_SUBROGACION = '13';
    const PAGO_POR_CONSIGNACION = '14';
    const CONDONACION = '15';
    const COMPENSACION = '17';
    const NOVACION = '23';
    const CONFUSION = '24';
    const REMISION_DE_DEUDA = '25';
    const PRESCRIPCION_O_CADUCIDAD = '26';
    const A_SATISFACCION_DEL_ACREEDOR = '27';
    const TARJETA_DE_DEBITO = '28';
    const TARJETA_DE_SERVICIOS = '29';
    const APLICACION_DE_ANTICIPOS = '30';
    const INTERMEDIARIO_PAGOS = '31';
    const POR_DEFINIR = '99';

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
            self::EFECTIVO => 'Efectivo',
            self::CHEQUE_NOMINATIVO => 'Cheque nominativo',
            self::TRANSFERENCIA_ELECTRONICA_DE_FONDOS => 'Transferencia electrónica de fondos',
            self::TARJETA_DE_CREDITO => 'Tarjeta de crédito',
            self::MONEDERO_ELECTRONICO => 'Monedero electrónico',
            self::DINERO_ELECTRONICO => 'Dinero electrónico',
            self::VALES_DE_DESPENSA => 'Vales de despensa',
            self::DACION_EN_PAGO => 'Dación en pago',
            self::PAGO_POR_SUBROGACION => 'Pago por subrogación',
            self::PAGO_POR_CONSIGNACION => 'Pago por consignación',
            self::CONDONACION => 'Condonación',
            self::COMPENSACION => 'Compensación',
            self::NOVACION => 'Novación',
            self::CONFUSION => 'Confusión',
            self::REMISION_DE_DEUDA => 'Remisión de deuda',
            self::PRESCRIPCION_O_CADUCIDAD => 'Prescripción o caducidad',
            self::A_SATISFACCION_DEL_ACREEDOR => 'A satisfacción del acreedor',
            self::TARJETA_DE_DEBITO => 'Tarjeta de débito',
            self::TARJETA_DE_SERVICIOS => 'Tarjeta de servicios',
            self::APLICACION_DE_ANTICIPOS => 'Aplicación de anticipos',
            self::INTERMEDIARIO_PAGOS => 'Intermediario pagos',
            self::POR_DEFINIR => 'Por definir'
        ];
    }
}
