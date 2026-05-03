<?php

use App\Console\Commands\SendUnansweredInquiryReminders;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send follow-up reminders for conversations where seller hasn't replied
Schedule::command(SendUnansweredInquiryReminders::class)->hourly();
