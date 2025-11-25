<?php

namespace App\Classes\Sat;

class RegimenFiscal
{
    const GENERAL_LEY_PERSONAS_MORALES = '601';
    const PERSONAS_MORALES_FINES_NO_LUCRATIVOS = '603';
    const SUELDOS_INGRESOS_ASIMILADOS_A_SALARIOS = '605';
    const ARRENDAMIENTO = '606';
    const DEMAS_INGRESOS = '608';
    const CONSOLIDACION = '609';
    const RESIDENTES_EXTRANJERO_SIN_ESTABLECIMIENTO_PERMANENTE_EN_MEXICO = '610';
    const INGRESOS_DIVIDENDOS = '611';
    const PERSONAS_FISICAS_ACTIVIDADES_EMPRESARIALES_PROFESIONALES = '612';
    const INGRESOS_INTERESES = '614';
    const SIN_OBLIGACIONES_FISCALES = '616';
    const SOCIEDADES_COOPERATIVAS_DIFIEREN_INGRESOS = '620';
    const INCORPORACION_FISCAL = '621';
    const ACTIVIDADES_AGRICOLAS_GANADERAS_SILVICOLAS_PESQUERAS = '622';
    const OPCIONAL_GRUPOS_DE_SOCIEDADES = '623';
    const COORDINADOS = '624';
    const HIDROCARBUROS = '628';
    const RÉGIMEN_ENAJENACION_ADQUISICIÓN_BIENES = '607';
    const REGIMENES_FISCALES_PREFERENTES_EMPRESAS_MULTINACIONALES = '629';
    const ENAJENACION_ACCIONES_BOLSA_VALORES = '630';
    const RÉGIMEN_INGRESOS_OBTENCIÓN_PREMIOS = '615';
    const RÉGIMEN_SIMPLIFICADO_DE_CONFIANZA = '626';
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
            self::GENERAL_LEY_PERSONAS_MORALES => 'General de Ley Personas Morales',
            self::PERSONAS_MORALES_FINES_NO_LUCRATIVOS => 'Personas Morales con Fines no Lucrativos',
            self::SUELDOS_INGRESOS_ASIMILADOS_A_SALARIOS => 'Sueldos y Salarios e Ingresos Asimilados a Salarios',
            self::ARRENDAMIENTO => 'Arrendamiento',
            self::DEMAS_INGRESOS => 'Demás ingresos',
            self::CONSOLIDACION => 'Consolidación',
            self::RESIDENTES_EXTRANJERO_SIN_ESTABLECIMIENTO_PERMANENTE_EN_MEXICO => 'Residentes en el Extranjero sin Establecimiento Permanente en México',
            self::INGRESOS_DIVIDENDOS => 'Ingresos por Dividendos (socios y accionistas)',
            self::PERSONAS_FISICAS_ACTIVIDADES_EMPRESARIALES_PROFESIONALES => 'Personas Físicas con Actividades Empresariales y Profesionales',
            self::INGRESOS_INTERESES => 'Ingresos por intereses',
            self::SIN_OBLIGACIONES_FISCALES => 'Sin obligaciones fiscales',
            self::SOCIEDADES_COOPERATIVAS_DIFIEREN_INGRESOS => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos',
            self::INCORPORACION_FISCAL => 'Incorporación Fiscal',
            self::ACTIVIDADES_AGRICOLAS_GANADERAS_SILVICOLAS_PESQUERAS => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
            self::OPCIONAL_GRUPOS_DE_SOCIEDADES => 'Opcional para Grupos de Sociedades',
            self::COORDINADOS => 'Coordinados',
            self::HIDROCARBUROS => 'Hidrocarburos',
            self::RÉGIMEN_ENAJENACION_ADQUISICIÓN_BIENES => 'Régimen de Enajenación o Adquisición de Bienes',
            self::REGIMENES_FISCALES_PREFERENTES_EMPRESAS_MULTINACIONALES => 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales',
            self::ENAJENACION_ACCIONES_BOLSA_VALORES => 'Enajenación de acciones en bolsa de valores',
            self::RÉGIMEN_INGRESOS_OBTENCIÓN_PREMIOS => 'Régimen de los ingresos por obtención de premios',
            self::RÉGIMEN_SIMPLIFICADO_DE_CONFIANZA => 'Régimen Simplificado de confianza',
        ];
    }
}
