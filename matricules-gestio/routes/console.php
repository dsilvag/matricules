<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    \App\Models\StreetBarriVell::penjarVehicles();
})->dailyAt('9:43');

Schedule::command('sync:oracle')->dailyAt('9:56');