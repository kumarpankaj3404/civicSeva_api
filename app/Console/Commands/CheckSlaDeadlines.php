<?php

namespace App\Console\Commands;

use App\Jobs\GrievanceEscalationJob;
use App\Models\Application;
use Illuminate\Console\Command;

class CheckSlaDeadlines extends Command
{
    protected $signature   = 'civicseva:check-sla';
    protected $description = 'Find overdue applications past their SLA deadline and escalate.';

    public function handle(): int
    {
        $overdue = Application::overdueSla()->get();

        if ($overdue->isEmpty()) {
            $this->info('No overdue applications found.');
            return self::SUCCESS;
        }

        $this->info("Found {$overdue->count()} overdue application(s). Dispatching escalation jobs...");

        foreach ($overdue as $application) {
            GrievanceEscalationJob::dispatch($application)->onQueue('escalations');
            $this->line(" → Dispatched for application: {$application->_id}");
        }

        return self::SUCCESS;
    }
}
