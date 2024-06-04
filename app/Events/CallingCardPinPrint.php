<?php

namespace App\Events;

use App\Models\CallingCardPin;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CallingCardPinPrint
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pin;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(CallingCardPin $pin)
    {
        $this->pin = $pin;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
