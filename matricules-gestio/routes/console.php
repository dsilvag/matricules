<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    \App\Models\StreetBarriVell::penjarVehicles();
})->dailyAt('00:05');

Schedule::command('app:sync-oracle-to-mysql-street')->dailyAt('00:05');
Schedule::command('app:sync-oracle-to-mysql-dwelling')->dailyAt('00:05');
Schedule::command('app:sync-oracle-to-mysql-people')->dailyAt('00:05');
Schedule::command('app:sync-oracle-to-mysql-teleco')->dailyAt('00:05');