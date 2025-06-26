<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutPatient
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    protected $msg;
    protected $scretary_id;
    public function __construct($msg, $scretary_id)
    {
        $this->msg = $msg;
        $this->scretary_id = $scretary_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('out-patient.' . $this->scretary_id),
        ];
    }
    public function broadcastWith()
    {
        return [
            'scretary_id' => $this->scretary_id,
            'message' => $this->msg,
            'timestamp' => now()->toDateTimeString()
        ];
    }
    public function broadcastAs(): string
    {
        return 'patient.outed';
    }
}
