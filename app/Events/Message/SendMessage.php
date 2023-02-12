<?php

namespace App\Events\Message;

use App\Http\Resources\OneMessageResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $new_message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($new_message)
    {
        $this->new_message = $new_message;
    }

    public function broadcastAs(): string
    {
        return 'message-sent';
    }

    public function  broadcastWith(): array
    {
        return [
            'message'=>  new OneMessageResource($this->new_message),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): Channel|PrivateChannel|array
    {
        return new Channel('Staff-Management');
    }
}
