<?php

namespace App\Events\Conversation;

use App\Http\Resources\ConversationResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Conversation implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $con;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($con)
    {
        $this->con = $con;
    }

    public function broadcastAs(): string
    {
        return 'conversation-created';
    }

    public function  broadcastWith(): array
    {
        return [
            'conversation'=>  new ConversationResource($this->con),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn(): Channel|array
    {
        return new Channel('Staff-Management');
    }
}
