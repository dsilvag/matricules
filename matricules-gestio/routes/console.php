<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    \App\Models\StreetBarriVell::penjarVehicles();
})->dailyAt('7:00');

Schedule::command('app:sync-oracle-to-mysql-street')->dailyAt('7:00');
Schedule::command('app:sync-oracle-to-mysql-dwelling')->dailyAt('7:00');
Schedule::command('app:sync-oracle-to-mysql-people')->dailyAt('7:00');
Schedule::command('app:sync-oracle-to-mysql-teleco')->dailyAt('7:00');