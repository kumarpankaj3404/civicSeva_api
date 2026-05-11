<?php

namespace App\Jobs;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GrievanceEscalationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(private Application $application) {}

    /**
     * Escalate an overdue application.
     * In production: send notification to admin, create ticket, etc.
     */
    public function handle(): void
    {
        Log::warning('[SLA] Application overdue — escalating.', [
            'application_id' => (string) $this->application->_id,
            'user_id'        => $this->application->user_id,
            'scheme_id'      => $this->application->scheme_id,
            'sla_deadline'   => $this->application->sla_deadline?->toIso8601String(),
            'status'         => $this->application->status,
        ]);

        // Mark as escalated (extend SLA by 7 days for now)
        $this->application->update([
            'status'       => 'under_review',
            'sla_deadline' => now()->addDays(7),
            'notes'        => ($this->application->notes ?? '').
                              "\n[AUTO] SLA breached on ".now()->toDateString().'. Escalated.',
        ]);

        // TODO: Dispatch notification to admin email / SMS
        // Notification::route('mail', config('app.admin_email'))->notify(new SlaBreachNotification($this->application));
    }
}
