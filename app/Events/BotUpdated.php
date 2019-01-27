<?php

namespace App\Events;

use App\Update;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BotUpdated implements ShouldBroadcast
{
    use SerializesModels;

    protected $fullVersion;
    protected $changes;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Update $update)
    {
        $this->fullVersion = $update->full_version;
        $this->changes     = $update->changes;
    }

    public function broadcastWith()
    {
        return [
            'version' => $this->fullVersion,
            'changes' => $this->changes,
        ];
    }

    public function broadcastAs()
    {
        return 'bot.updated';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new Channel('public');
    }
}