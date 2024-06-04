<?php

namespace App\Events;

use App\Models\RateTable;
use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RateTablePush
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $rateTable;
    public $userID;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RateTable $rateTable,$userID)
    {
        $this->rateTable = $rateTable;
        $this->userID = $userID;
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
