<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FilterHelper {

    public static function numericRange(string $field): callable {
        return function (Builder $query, $value) use ($field) {
            try {
                if (is_array($value)) {
                    $start = !empty($value[0]) ? Carbon::parse($value[0])->startOfDay() : null;
                    $end = !empty($value[1]) ? Carbon::parse($value[1])->endOfDay() : null;

                    if ($start && $end) {
                        if ($start->gt($end)) {
                            [$start, $end] = [$end, $start];
                        }
                        $query->whereBetween($field, [$start, $end]);
                    } elseif ($start) {
                        $query->where($field, '>=', $start);
                    } elseif ($end) {
                        $query->where($field, '<=', $end);
                    }
                }
                elseif (is_string($value)) {
                    $date = Carbon::parse($value);
                    $query->whereDate($field, $date);
                }
                elseif ($value instanceof Carbon) {
                    $query->whereDate($field, $value);
                }
            } catch (\Exception $e) {
                Log::error("Error applying date filter for field {$field}: ".$e->getMessage());
                throw new \InvalidArgumentException("Formato de fecha inválido para el campo {$field}");
            }
        };

    }

    public static function dateRange(string $field): callable {
        return function (Builder $query, $value) use ($field) {
            try {
                if (is_string($value) && strpos($value, ',') !== false) {
                    $value = explode(',', $value, 2);
                }

                if (is_array($value)) {
                    $startValue = ($value[0] !== null && $value[0] !== 'undefined' && $value[0] !== '')
                                ? $value[0] : null;

                    $endValue = (isset($value[1]) && $value[1] !== null && $value[1] !== 'undefined' && $value[1] !== '')
                                ? $value[1] : null;

                    $start = $startValue ? Carbon::parse($startValue)->startOfDay() : null;
                    $end = $endValue ? Carbon::parse($endValue)->endOfDay() : null;

                    if ($start && $end) {
                        if ($start->gt($end)) {
                            [$start, $end] = [$end, $start]; // Ordenar fechas
                        }
                        $query->whereBetween($field, [$start, $end]);
                    } elseif ($start) {
                        $query->where($field, '>=', $start);
                    } elseif ($end) {
                        $query->where($field, '<=', $end);
                    }
                } elseif (is_string($value) && $value !== 'undefined' && $value !== '') {
                    $date = Carbon::parse($value);
                    $query->whereDate($field, $date);
                }
            } catch (\Exception $e) {
                Log::error("Error applying date filter for field {$field}: ".$e->getMessage());
                throw new \InvalidArgumentException("Formato de fecha inválido para el campo {$field}");
            }

            Log::info( $query->toRawSql() );
    };
}
}
