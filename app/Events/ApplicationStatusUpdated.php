<?php

namespace App\Events;

use App\Models\Application;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Application $application) {}

    /**
     * Broadcast on the user's private channel.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->application->user_id),
        ];
    }

    /**
     * The event name received by the frontend.
     */
    public function broadcastAs(): string
    {
        return 'application.status.updated';
    }

    /**
     * Payload sent to the frontend.
     */
    public function broadcastWith(): array
    {
        return [
            'application_id' => (string) $this->application->_id,
            'scheme_id'      => $this->application->scheme_id,
            'status'         => $this->application->status,
            'updated_at'     => $this->application->updated_at?->toIso8601String(),
        ];
    }
}
