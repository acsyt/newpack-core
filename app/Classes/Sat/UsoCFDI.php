<?php

namespace App\Classes\Sat;

class UsoCFDI
{
    const ADQUISICION_MERCANCIAS = 'G01';
    const DEVOLUCIONES_DESCUENTOS_BONIFICACIONES = 'G02';
    const GASTOS_GENERALES = 'G03';
    const CONSTRUCCIONES = 'I01';
    const MOBILARIO_EQUIPO_OFICINA_POR_INVERSIONES = 'I02';
    const EQUIPO_TRANSPORTE = 'I03';
    const EQUIPO_COMPUTO_ACCESORIOS = 'I04';
    const DADO_TROQUELES_MOLDES_MATRICES_HERRAMENTAL = 'I05';
    const COMUNICACIONES_TELEFONICAS = 'I06';
    const COMUNICACIONES_SATELITALES = 'I07';
    const OTRA_MAQUINARIA_EQUIPO = 'I08';
    const HONORARIOS_MEDICOS_DENTALES_GASTOS_HOSPITALARIOS = 'D01';
    const GASTOS_MEDICOS_INCAPACIDAD_DISCAPACIDAD = 'D02';
    const GASTOS_FUNERALES = 'D03';
    const DONATIVOS = 'D04';
    const INTERESES_REALES_CREDITOS_HIPOTECARIOS = 'D05';
    const APORTACIONES_VOLUNTARIAS_SAR = 'D06';
    const PRIMAS_SEGUROS_GASTOS_MEDICOS = 'D07';
    const GASTOS_TRANSPORTACIÓN_ESCOLAR_OBLIGATORIA = 'D08';
    const DEPÓSITOS_AHORRO_PENSIONES = 'D09';
    const PAGOS_SERVICIOS_EDUCATIVOS_COLEGIATURAS = 'D10';
    const POR_DEFINIR = 'P01';
    const SIN_EFECTOS_FISCALES = 'S01';

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
            self::ADQUISICION_MERCANCIAS => 'Adquisición de mercancias',
            self::DEVOLUCIONES_DESCUENTOS_BONIFICACIONES => 'Devoluciones, descuentos o bonificaciones',
            self::GASTOS_GENERALES => 'Gastos en general',
            self::CONSTRUCCIONES => 'Construcciones',
            self::MOBILARIO_EQUIPO_OFICINA_POR_INVERSIONES => 'Mobilario y equipo de oficina por inversiones',
            self::EQUIPO_TRANSPORTE => 'Equipo de transporte',
            self::EQUIPO_COMPUTO_ACCESORIOS => 'Equipo de computo y accesorios',
            self::DADO_TROQUELES_MOLDES_MATRICES_HERRAMENTAL => 'Dados, troqueles, moldes, matrices y herramental',
            self::COMUNICACIONES_TELEFONICAS => 'Comunicaciones telefónicas',
            self::COMUNICACIONES_SATELITALES => 'Comunicaciones satelitales',
            self::OTRA_MAQUINARIA_EQUIPO => 'Otra maquinaria y equipo',
            self::HONORARIOS_MEDICOS_DENTALES_GASTOS_HOSPITALARIOS => 'Honorarios médicos, dentales y gastos hospitalarios.',
            self::GASTOS_MEDICOS_INCAPACIDAD_DISCAPACIDAD => 'Gastos médicos por incapacidad o discapacidad',
            self::GASTOS_FUNERALES => 'Gastos funerales.',
            self::DONATIVOS => 'Donativos.',
            self::INTERESES_REALES_CREDITOS_HIPOTECARIOS => 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación).',
            self::APORTACIONES_VOLUNTARIAS_SAR => 'Aportaciones voluntarias al SAR.',
            self::PRIMAS_SEGUROS_GASTOS_MEDICOS => 'Primas por seguros de gastos médicos.',
            self::GASTOS_TRANSPORTACIÓN_ESCOLAR_OBLIGATORIA => 'Gastos de transportación escolar obligatoria.',
            self::DEPÓSITOS_AHORRO_PENSIONES => 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.',
            self::PAGOS_SERVICIOS_EDUCATIVOS_COLEGIATURAS => 'Pagos por servicios educativos (colegiaturas)',
            self::POR_DEFINIR => 'Por definir',
            self::SIN_EFECTOS_FISCALES => 'Sin efectos fiscales',

        ];
    }
}
