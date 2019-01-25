<?php

namespace App\Events;

use App\PlayerTrack;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class TrackedPlayerLost implements ShouldBroadcastNow
{
    use SerializesModels;

    protected $player;
    protected $last;
    protected $last_seen;
    protected $guildid;
    protected $channelid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PlayerTrack $playerTrack, $last_seen)
    {
        $this->player    = $playerTrack->player;
        $this->last      = $playerTrack->last_coordinate;
        $this->last_seen = $last_seen;

        $this->guildid   = $playerTrack->guild_id;
        $this->channelid = $playerTrack->channel_id;
    }

    public function broadcastWith()
    {
        return [
            'player'    => $this->player,
            'last'      => $this->last,
            'last_seen' => $this->last_seen,
            'guildid'   => $this->guildid,
            'channelid' => $this->channelid,
        ];
    }

    public function broadcastAs()
    {
        return 'tracked.player.lost';
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
