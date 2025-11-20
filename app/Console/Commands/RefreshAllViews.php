<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshAllViews extends Command
{
    protected $signature = 'view:refresh-all {--table=* : Specific tables to refresh}';
    protected $description = 'Refresh all views (residents, spots, etc.)';

    public function handle()
    {
        $tables = $this->option('table');

        if (empty($tables)) {
            $tables = ['users'];
        }

        foreach ($tables as $table) {
            $this->info("Refreshing {$table} view...");

            $exitCode = Artisan::call("view:refresh-{$table}");

            if ($exitCode === 0) {
                $this->info("✅ {$table} view refreshed successfully");
            } else {
                $this->error("❌ Failed to refresh {$table} view");
            }
        }

        $this->info('All views refreshed!');
    }
}
