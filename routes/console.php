<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('dr:snapshot --type=snapshot')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('dr:snapshot --type=daily --full')->dailyAt('02:00')->withoutOverlapping();
Schedule::command('dr:snapshot --type=monthly --full')->monthlyOn(1, '03:00')->withoutOverlapping();
Schedule::command('dr:mirror-cloud')->everyMinute()->withoutOverlapping();
Schedule::command('dr:replicate-pull --iterations=4 --interval=15')->everyMinute()->withoutOverlapping();
Schedule::command('dr:prune')->dailyAt('04:00')->withoutOverlapping();
