<?php

namespace App\Events;

use App\PlayerTrack;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TrackExpired implements ShouldBroadcast
{
    use SerializesModels;

    protected $player;
    protected $last;
    protected $guildid;
    protected $channelid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PlayerTrack $playerTrack)
    {
        $this->player = $playerTrack->player;
        $this->last   = $playerTrack->last_coordinate;

        $this->guildid   = $playerTrack->guild_id;
        $this->channelid = $playerTrack->channel_id;
    }

    public function broadcastWith()
    {
        return [
            'player'    => $this->player,
            'last'      => $this->last,
            'guildid'   => $this->guildid,
            'channelid' => $this->channelid,
        ];
    }

    public function broadcastAs()
    {
        return 'track.expired';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('public');
    }
}
