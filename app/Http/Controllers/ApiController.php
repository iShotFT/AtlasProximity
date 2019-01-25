<?php

namespace App\Http\Controllers;

use App\Classes\Coordinate;
use App\Events\TrackedPlayerMoved;
use App\Events\TrackExpired;
use App\PlayerPing;
use App\PlayerTrack;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
    public static function track()
    {
        foreach (PlayerTrack::get() as $player_track) {
            if ($player_track->until <= Carbon::now()) {
                // remove track
                $player_track->delete();
                event(new TrackExpired($player_track));
            } else {
                // Valid track
                if ($player_ping = PlayerPing::where('player', $player_track->player)->orderByDesc('updated_at')->first()) {
                    // Scan the server the player was last seen on
                    $remained_stationairy = false;
                    foreach (SourceQueryController::getCoordinatePlayers($player_track->last_coordinate)['players'] as $player) {
                        // Is the tracked player still on the server we last saw him on?
                        if ($player_track->player === $player['Name']) {
                            $remained_stationairy = true;
                        }
                    }

                    if ($remained_stationairy) {
                        // We found the player on the same server as last time we spotted him
                        // No need for alert, we will update the playerping so we know we checked him out
                        $player_track->update([
                            'updated_at' => Carbon::now(),
                        ]);
                    } else {
                        // The player is no longer on the same server where we last spotted him... He might have gone offline or he might have moved
                        // Scan the servers around the last_coordinate for his name
                        $original_coordinate = $player_track->last_coordinate;
                        $found_in            = false;
                        foreach (SourceQueryController::getCoordinatePlayersWithSurrounding($player_track->last_coordinate) as $coordinate => $server_info) {
                            foreach ($server_info['players'] as $player) {
                                if ($player_track->player === $player['Name']) {
                                    $found_in = $coordinate;
                                }
                            }
                        }

                        if ($found_in) {
                            // Player with the same name was found in a neighbouring server
                            $current_coordinate = $found_in;
                        } else {
                            // Player was not found in the original server, nor in any of the 8 servers around it
                            // Player might have gone offline or teleported / died
                            $current_coordinate = $player_ping->coordinates;
                        }

                        list ($x1, $y1) = Coordinate::textToXY($original_coordinate);
                        list ($x2, $y2) = Coordinate::textToXY($current_coordinate);
                        $last_direction = Coordinate::cardinalDirectionBetween($x1, $y1, $x2, $y2);

                        $player_track->update([
                            'last_coordinate' => $current_coordinate,
                            'last_direction'  => $last_direction,
                        ]);

                        // Player moved since last track
                        // Trigger event to warn the tracking server about this movement
                        event(new TrackedPlayerMoved($player_track, $original_coordinate));
                    }
                } else {
                    // No player in playerping found with this name???
                }
            }
        }
    }

    public function players(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        return response()->json(SourceQueryController::getCoordinatePlayers($request->get('server'), $request->get('region'), $request->get('gamemode')));
    }

    public function population(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        return response()->json(SourceQueryController::getCoordinatePlayersWithSurrounding($request->get('server'), $request->get('region'), $request->get('gamemode')));
    }

    public function find(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:2',
        ]);

        $found = PlayerPing::where('player', '=', $request->get('username'))->orderByDesc('updated_at')->limit(5)->get([
            'player',
            'coordinates',
            'updated_at',
        ]);

        return response()->json(($found ? $found->toArray() : []));
    }

    public function trackAdd(Request $request)
    {
        $request->validate([
            'username'  => 'required|string|min:2',
            'minutes'   => 'required',
            'guildid'   => 'required',
            'channelid' => 'required',
        ]);

        // First make sure we can find the player in the tracking DB
        if ($last_ping = PlayerPing::where('player', $request->get('username'))->where('updated_at', '>=', Carbon::now()->subMinutes(10))->orderByDesc('updated_at')->first()) {
            // Will overwrite a track if one already exists for this guild and player
            $playertrack = PlayerTrack::updateOrCreate([
                'guild_id' => $request->get('guildid'),
                'player'   => $request->get('username'),
            ], [
                'channel_id' => $request->get('channelid'),
                'until'      => Carbon::now()->addMinutes($request->get('minutes')),
            ]);

            $playertrack->update([
                'last_coordinate' => $last_ping->coordinates,
            ]);
        } else {
            return response()->json(['message' => 'User ' . $request->get('username') . ' not found on any server in the past 10 minutes'], 404);
        }
    }

    public function trackList(Request $request)
    {
        $request->validate([
            'guildid' => 'required|integer',
        ]);

        $found = PlayerTrack::where('guild_id', $request->get('guildid'))->where('until', '>=', Carbon::now())->get([
            'player',
            'last_coordinate',
            'updated_at',
            'until',
        ]);

        return response()->json(($found ? $found->toArray() : []));
    }
}
