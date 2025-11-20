<?php

namespace App\Console\Commands\Views;

class RefreshUsersView extends RefreshCentralViewCommand
{
    protected $signature = 'view:refresh-users';
    protected $description = 'Refresh central users view';

    protected function getTableName(): string
    {
        return 'users';
    }

    protected function getViewName(): string
    {
        return 'v_users';
    }
}
