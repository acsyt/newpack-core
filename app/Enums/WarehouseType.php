<?php

namespace App\Enums;

class WarehouseType {

    const MAIN = 'main';
    const SECONDARY = 'secondary';
    const STORE = 'store';

    public static function humanReadableType($type): string {
        return match ($type) {
            self::MAIN       => 'Principal',
            self::SECONDARY  => 'Secundario ',
            self::STORE      => 'Mostrador',
            default          => 'Desconocido',
        };
    }

    public static function typeList(): array {
        return [
            self::MAIN,
            self::SECONDARY,
            self::STORE,
        ];
    }

    public static function humanReadableTypeList(): array {
        $types = self::typeList();
        return array_map(fn($type) => self::humanReadableType($type), $types);
    }

}
