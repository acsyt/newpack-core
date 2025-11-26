<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('update:daily-exchange-rate')
    ->timezone('America/Mexico_City')
    ->daily()
    ->at('00:00')
    ->appendOutputTo(storage_path('logs/update_daily_exchange_rate.log'));
