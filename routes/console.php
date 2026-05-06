<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

app()->booted(function (): void {
    $schedule = app(Schedule::class);

    $schedule->command('cache:clear')
        ->dailyAt('03:00')
        ->onOneServer()
        ->withoutOverlapping();

    $schedule->command('cache:warm-app all --chunk=200')
        ->dailyAt('03:10')
        ->onOneServer()
        ->withoutOverlapping();
});
