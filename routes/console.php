<?php

use App\Support\EventPlanner\EventReminderService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('events:send-reminders', function (EventReminderService $eventReminderService) {
    $sent = $eventReminderService->sendDueReminders();

    $this->info(sprintf('Sent %d reminder(s).', $sent));
})->purpose('Send due event reminders');

Schedule::command('events:send-reminders')->everyFifteenMinutes();
