<?php

namespace App\StateMachines;

use Asantibanez\LaravelEloquentStateMachines\StateMachines\StateMachine;

class SupplierStatusStateMachine extends StateMachine
{
    public const ACTIVE = 'active';
    public const INACTIVE = 'inactive';
    public const SUSPENDED = 'suspended';
    public const BLACKLISTED = 'blacklisted';

    public function defaultState(): ?string {
        return self::ACTIVE;
    }

    public function transitions(): array {
        return [
            self::INACTIVE => [self::ACTIVE],
            self::ACTIVE => [self::INACTIVE, self::SUSPENDED, self::BLACKLISTED],
            self::SUSPENDED => [self::ACTIVE, self::BLACKLISTED],
            self::BLACKLISTED => [self::ACTIVE],
        ];
    }

    public function recordHistory(): bool {
        return true;
    }
}

