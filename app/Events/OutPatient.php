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
    protected $patient_id;
    public function __construct($msg, $patient_id)
    {
        $this->msg = $msg;
        $this->patient_id = $patient_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('out-patient.' . $this->patient_id),
        ];
    }
    public function broadcastWith()
    {
        return [
            'patient_id' => $this->patient_id,
            'message' => $this->msg,
            'timestamp' => now()->toDateTimeString()
        ];
    }
    public function broadcastAs(): string
    {
        return 'patient.outed';
    }
}
