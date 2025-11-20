<?php

namespace App\StateMachines;

use Asantibanez\LaravelEloquentStateMachines\StateMachines\StateMachine;

abstract class BaseStateMachine extends StateMachine
{
    protected static string $translationPrefix = '';
    protected static string $statusColumn = 'status';

    public function recordHistory(): bool
    {
        return true;
    }


    public static function getHumanReadableStatus(string $status): string
    {
        $key = static::$translationPrefix . $status;
        $translated = __($key);

        return $translated === $key
            ? ucfirst(str_replace('_', ' ', $status))
            : $translated;
    }

    public static function getHumanStatuses(): array
    {
        $statuses = [];

        foreach ((new \ReflectionClass(static::class))->getConstants() as $name => $value) {
            if (is_string($value)) {
                $statuses[$value] = static::getHumanReadableStatus($value);
            }
        }

        return $statuses;
    }

    public static function getHumanStatusStatement(string | null $column = null): string
    {
        $column = $column ?? static::$statusColumn;
        $statusMap = static::getHumanStatuses();

        $case = "CASE";
        foreach ($statusMap as $status => $label) {
            $safeStatus = str_replace("'", "''", $status);
            $safeLabel = str_replace("'", "''", $label);
            $case .= " WHEN {$column} = '{$safeStatus}' THEN '{$safeLabel}'";
        }
        $case .= " END";

        return $case;
    }
}
