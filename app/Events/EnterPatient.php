<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnterPatient implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    protected $msg;
    protected $id;
    protected $patientInfo;
    protected $preview;
    public function __construct($msg, $doctorId, $patientInfo,$preview)
    {
        $this->msg = $msg;
        $this->id = $doctorId;
        $this->patientInfo = $patientInfo;
        $this->preview=$preview;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('enter-patient.' . $this->id),
        ];
    }
    public function broadcastWith()
    {
        return [
            'patient' => $this->patientInfo,
            'preview'=>$this->preview,
            'message' => $this->msg,
            'timestamp' => now()->toDateTimeString()
        ];
    }
    public function broadcastAs(): string
    {
        return 'patient.entered'; // Listen for `.patient.entered` instead of full class path
    }
}