<?php

namespace App\Jobs;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessBlockchainLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum number of retry attempts.
     */
    public int $tries = 3;

    /**
     * Retry after N seconds.
     */
    public int $backoff = 60;

    public function __construct(private Application $application) {}

    /**
     * Execute the job.
     * Posts a SHA-256 hash of the application data to the blockchain bridge.
     */
    public function handle(): void
    {
        $bridgeUrl = config('app.blockchain_bridge_url', env('BLOCKCHAIN_BRIDGE_URL'));

        // Generate a deterministic hash of the application
        $hash = hash('sha256', json_encode([
            'application_id' => (string) $this->application->_id,
            'user_id'        => $this->application->user_id,
            'scheme_id'      => $this->application->scheme_id,
            'interview_data' => $this->application->interview_data,
            'submitted_at'   => $this->application->created_at?->toIso8601String(),
        ]));

        if (! $bridgeUrl) {
            // Dev mode: just store the hash locally without calling external service
            Log::info('[Blockchain] Bridge URL not configured. Storing hash locally.', [
                'application_id' => (string) $this->application->_id,
                'hash'           => $hash,
            ]);

            $this->application->update([
                'blockchain_hash'       => $hash,
                'blockchain_logged_at'  => now(),
            ]);
            return;
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders(['Authorization' => 'Bearer '.env('AI_GATEWAY_SECRET')])
                ->post($bridgeUrl.'/log', [
                    'application_id' => (string) $this->application->_id,
                    'hash'           => $hash,
                    'timestamp'      => now()->toIso8601String(),
                ]);

            if ($response->successful()) {
                $this->application->update([
                    'blockchain_hash'      => $hash,
                    'blockchain_logged_at' => now(),
                ]);

                Log::info('[Blockchain] Successfully logged application.', [
                    'application_id' => (string) $this->application->_id,
                ]);
            } else {
                Log::warning('[Blockchain] Bridge returned non-200 response.', [
                    'status'         => $response->status(),
                    'application_id' => (string) $this->application->_id,
                ]);
                $this->fail('Blockchain bridge returned: '.$response->status());
            }
        } catch (\Throwable $e) {
            Log::error('[Blockchain] Exception during logging.', [
                'error'          => $e->getMessage(),
                'application_id' => (string) $this->application->_id,
            ]);
            throw $e;
        }
    }
}
