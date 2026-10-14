<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sinkronisasi otomatis data OPD dari API Orpeg setiap hari jam 02:00 WITA
Schedule::command('orpeg:sync-organizations')
    ->dailyAt('02:00')
    ->withoutOverlapping();

