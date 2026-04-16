<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule automated tasks
Schedule::command('reminders:deadlines')->dailyAt('08:00');
Schedule::command('books:mark-overdue')->hourly();
Schedule::command('subscriptions:check-expiry')->dailyAt('00:01');
