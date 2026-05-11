<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── CivicConnect Scheduled Tasks ────────────────────────────────────────────

// Check for SLA-breached applications every hour and dispatch escalation jobs
Schedule::command('civicseva:check-sla')->hourly()->withoutOverlapping();
