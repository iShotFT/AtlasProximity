<?php

namespace App\Http\Controllers;

use App\Events\TrackedPlayerMoved;
use App\PlayerPing;
use App\PlayerTrack;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public static function track()
    {
        foreach (PlayerTrack::get() as $player_track) {
            if ($player_track->until <= Carbon::now()) {
                // remove track
                $player_track->delete();
            } else {
                // Valid track
                if ($player_ping = PlayerPing::where('player', $player_track->player)->orderByDesc('updated_at')->first()) {
                    if ($player_ping->coordinates !== $player_track->last_coordinate) {
                        $original_coordinate = $player_track->last_coordinate;
                        // Player moved since last track
                        $player_track->update([
                            'last_coordinate' => $player_ping->coordinates,
                        ]);

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
            'until',
        ]);

        return response()->json(($found ? $found->toArray() : []));
    }
}
