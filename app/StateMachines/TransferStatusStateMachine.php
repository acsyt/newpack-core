<?php

namespace App\StateMachines;

use Asantibanez\LaravelEloquentStateMachines\StateMachines\StateMachine;

class TransferStatusStateMachine extends StateMachine
{
    public const REQUESTED = 'requested';
    public const SHIPPED = 'shipped';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';

    public function defaultState(): ?string
    {
        return self::REQUESTED;
    }

    public function transitions(): array
    {
        return [
            self::REQUESTED => [self::SHIPPED, self::CANCELLED],
            self::SHIPPED => [self::COMPLETED, self::CANCELLED],
            self::COMPLETED => [],
            self::CANCELLED => [],
        ];
    }

    public function recordHistory(): bool
    {
        return true;
    }
}
