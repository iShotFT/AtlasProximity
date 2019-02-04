<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Announcement implements ShouldBroadcast
{
    use SerializesModels;

    protected $callback;
    protected $title;
    protected $message;
    protected $channels;
    protected $mention;

    /**
     * Create a new event instance.
     *
     * @param \App\Announcement $announcement
     */
    public function __construct(\App\Announcement $announcement)
    {
        $this->callback = route('api.announcement.callback', compact('announcement'));
        $this->title    = $announcement->title;
        $this->message  = $announcement->message;
        $this->channels = $announcement->channels;
        $this->mention  = $announcement->mention;
    }

    public function broadcastWith()
    {
        return [
            'callback' => $this->callback,
            'title'    => $this->title,
            'message'  => $this->message,
            'channels' => $this->channels,
            'mention'  => $this->mention,
        ];
    }

    public function broadcastAs()
    {
        return 'announcement';
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
