<?php

namespace App\Events;

use App\PlayerTrack;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TrackedPlayerMoved implements ShouldBroadcast
{
    use SerializesModels;

    protected $from;
    protected $to;
    protected $player;
    protected $guildid;
    protected $channelid;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PlayerTrack $playerTrack, $oldcoordinates)
    {
        $this->player = $playerTrack->player;
        $this->from   = $oldcoordinates;
        $this->to     = $playerTrack->last_coordinate;

        $this->guildid   = $playerTrack->guild_id;
        $this->channelid = $playerTrack->channel_id;
    }

    public function broadcastWith()
    {
        return [
            'player'    => $this->player,
            'from'      => $this->from,
            'to'        => $this->to,
            'guildid'   => $this->guildid,
            'channelid' => $this->channelid,
        ];
    }

    public function broadcastAs()
    {
        return 'tracked.player.moved';
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
