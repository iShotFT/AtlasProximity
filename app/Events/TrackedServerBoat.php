<?php

namespace App\Events;

use App\Boat;
use App\Classes\Coordinate;
use App\ProximityTrack;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TrackedServerBoat implements ShouldBroadcast
{
    use SerializesModels;

    protected $from;
    protected $to;
    protected $players;
    protected $direction;
    protected $guildid;
    protected $channelid;
    protected $boatid;

    public function __construct(ProximityTrack $proximityTrack, $players, $from, Boat $boat)
    {
        $this->from    = $from;
        $this->to      = $proximityTrack->coordinate;
        $this->players = $players;

        list ($x1, $y1) = Coordinate::textToXY($proximityTrack->coordinate);
        list ($x2, $y2) = Coordinate::textToXY($from);
        $this->direction = Coordinate::cardinalDirectionBetween($x1, $y1, $x2, $y2);

        $this->boatid    = $boat->id;
        $this->guildid   = $proximityTrack->guild_id;
        $this->channelid = $proximityTrack->channel_id;
    }

    public function broadcastWith()
    {
        return [
            'from'      => $this->from,
            'to'        => $this->to,
            'players'   => $this->players,
            'direction' => $this->direction,
            'boatid'    => $this->boatid,
            'guildid'   => $this->guildid,
            'channelid' => $this->channelid,
        ];
    }

    public function broadcastAs()
    {
        return 'tracked.server.boat';
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
