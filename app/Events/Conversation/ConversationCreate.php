<?php

namespace App\Events\Conversation;

use App\Http\Resources\ConversationResource;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationCreate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $createConversation;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($createConversation)
    {
        $this->createConversation = $createConversation;
    }

    public function broadcastAs(): string
    {
        return 'conversation-created';
    }

    public function  broadcastWith(): array
    {
        return [
            'conversation'=>  new ConversationResource($this->createConversation),
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
