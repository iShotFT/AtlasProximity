<?php

namespace App\Http\Controllers;

use App\Classes\Coordinate;
use App\Events\TrackedPlayerLost;
use App\Events\TrackedPlayerMoved;
use App\Events\TrackedPlayerRefound;
use App\Events\TrackedServerBoat;
use App\Events\TrackExpired;
use App\Ping;
use App\PlayerPing;
use App\PlayerTrack;
use App\ProximityTrack;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public static function proximity()
    {
        foreach (ProximityTrack::get() as $proximity_track) {
            // Get list of players from the previous scan
            if ($old_players = Ping::where('coordinates', $proximity_track->coordinate)->orderByDesc('created_at')->first()) {
                $previous_scanned_players = json_decode($old_players->info, true);
                if ($new_scanned_players = SourceQueryController::getPlayersOnCoordinate($proximity_track->coordinate)['players']) {
                    // We only want to compare usernames
                    $previous_scanned_players = array_column($previous_scanned_players, 'Name');
                    $new_scanned_players      = array_column($new_scanned_players, 'Name');

                    $new_players_after_scan = array_diff($new_scanned_players, $previous_scanned_players);

                    if (is_array($new_players_after_scan) && count($new_players_after_scan) >= 2) {
                        // 3 or more new players joined in the past minute!!!
                        // Combine each username with the previous server they were spotted on

                        // Build an array with all r
                        $locations = [];
                        foreach ($new_players_after_scan as $username) {
                            if ($username !== '123' && $username !== '') {
                                $player_previous_locations = PlayerPing::where('player', '=', $username)->orderByDesc('updated_at')->limit(2)->get([
                                    'player',
                                    'coordinates',
                                    'updated_at',
                                ]);

                                // We need at least two previous locations for this player (the current one and the previous one) before we take action.
                                if ($player_previous_locations->count() >= 2) {
                                    $player_most_recent_previous_location = $player_previous_locations->offsetGet(1);

                                    if (array_key_exists($player_most_recent_previous_location->coordinates, $locations)) {
                                        // Array exists for this location, push into it *lennyface*
                                        array_push($locations[$player_most_recent_previous_location->coordinates], $username);
                                    } else {
                                        // No array for this location exists, make one
                                        $locations[$player_most_recent_previous_location->coordinates] = [$username];
                                    }
                                }
                            } else {
                                // This is a username we're not tracking
                            }
                        }

                        foreach ($locations as $location => $players) {
                            if (is_array($players) && count($players) >= 2) {
                                // Remove the current alert hit from the next warning
                                unset($locations[$location]);

                                // Only trigger an BOAT alert when the count of players is 2 or more
                                // Trigger 'Boat entered server XXX from XXX'
                                event(new TrackedServerBoat($proximity_track, $players, $location));
                            }
                        }
                    }
                } else {
                    // New scan failed (returned false)
                };
            } else {
                // Never had a scan for this server stored in DB?
            }


        }
    }

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
                    foreach (SourceQueryController::getPlayersOnCoordinate($player_track->last_coordinate)['players'] as $player) {
                        // Is the tracked player still on the server we last saw him on?
                        if ($player_track->player === $player['Name']) {
                            $remained_stationairy = true;
                        }
                    }

                    if ($remained_stationairy) {
                        // We found the player on the same server as last time we spotted him
                        // No need for alert, we will update the playerping so we know we checked him out
                        // Trigger online warning
                        if ($player_track->last_status === 0 && $player_ping->updated_at >= Carbon::now()->subMinutes(10)) {
                            // User came back online!
                            event(new TrackedPlayerRefound($player_track));
                        }

                        $player_track->update([
                            'updated_at'  => Carbon::now(),
                            'last_status' => 1,
                        ]);
                    } else {
                        // The player is no longer on the same server where we last spotted him... He might have gone offline or he might have moved
                        // Scan the servers around the last_coordinate for his name
                        $original_coordinate = $player_track->last_coordinate;
                        $found_in            = false;
                        foreach (SourceQueryController::getPlayersOnCoordinateWithSurrounding($player_track->last_coordinate) as $coordinate => $server_info) {
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

                        $update_info = [
                            'last_coordinate' => $current_coordinate,
                            'last_direction'  => $last_direction,
                        ];

                        if ($player_track->last_status === 0 && $player_ping->updated_at >= Carbon::now()->subMinutes(10)) {
                            // User came back online!
                            $update_info['last_status'] = 1;
                            event(new TrackedPlayerRefound($player_track));
                        }

                        $player_track->update($update_info);

                        // Player moved since last track
                        // Trigger event to warn the tracking server about this movement
                        if ($player_track->last_coordinate !== $original_coordinate) {
                            event(new TrackedPlayerMoved($player_track, $original_coordinate));
                        } else {
                            // If the player ping is older than 15 minutes we can suspect the player went offline.
                            if ($player_ping->updated_at <= Carbon::now()->subMinutes(10) && $player_track->last_status === 1) {
                                // We suspect player went offline
                                event(new TrackedPlayerLost($player_track, $player_ping->updated_at));
                                $player_track->update([
                                    'last_status' => 0,
                                ]);
                            }
                        }
                    }
                } else {
                    // No player in playerping found with this name???
                }
            }
        }
    }

    public function map(Request $request)
    {
        $request->validate([
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        // Generate a table with all the servers (from the DB or cache) and send it back

    }

    public function players(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        return response()->json(SourceQueryController::getPlayersOnCoordinate($request->get('server'), $request->get('region'), $request->get('gamemode')));
    }

    public function population(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        return response()->json(SourceQueryController::getPlayersOnCoordinateWithSurrounding($request->get('server'), $request->get('region'), $request->get('gamemode')));
    }

    public function find(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:2',
            //            'region'   => 'required|string|size:2',
            //            'gamemode' => 'required|string|size:3',
        ]);

        $found = PlayerPing::where('player', '=', $request->get('username'))->orderByDesc('updated_at')->limit(5)->get([
            'player',
            'coordinates',
            'updated_at',
        ]);

        return response()->json(($found ? $found->toArray() : []));
    }

    public function proximityAdd(Request $request)
    {
        $request->validate([
            'coordinate' => 'required|string|min:2|max:3',
            'guildid'    => 'required',
            'channelid'  => 'required',
        ]);

        // Refresh the data
        SourceQueryController::getPlayersOnCoordinateWithSurrounding($request->get('coordinate'));

        ProximityTrack::updateOrCreate([
            'coordinate' => $request->get('coordinate'),
            'guild_id'   => $request->get('guildid'),
            'channel_id' => $request->get('channelid'),
        ], [
            'updated_at' => Carbon::now(),
        ]);
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

    public function proximityRemove(Request $request)
    {
        $request->validate([
            'coordinate' => 'required|string|min:2|max:3',
            'guildid'    => 'required',
            'channelid'  => 'required',
        ]);

        ProximityTrack::where([
            'coordinate' => $request->get('coordinate'),
            'guild_id'   => $request->get('guildid'),
            'channel_id' => $request->get('channelid'),
        ])->delete();
    }

    public function trackRemove(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:2',
            'guildid'  => 'required',
            //            'channelid' => 'required',
        ]);

        PlayerTrack::where([
            'player'   => $request->get('username'),
            'guild_id' => $request->get('guildid'),
            //            'channel_id' => $request->get('channelid'),
        ])->delete();
    }

    public function proximityList(Request $request)
    {
        $request->validate([
            'guildid' => 'required|integer',
        ]);

        $found = ProximityTrack::where('guild_id', $request->get('guildid'))->get([
            'coordinate',
            'updated_at',
        ]);

        return response()->json(($found ? $found->toArray() : []));
    }

    public function trackList(Request $request)
    {
        $request->validate([
            'guildid' => 'required|integer',
        ]);

        $found = PlayerTrack::where('guild_id', $request->get('guildid'))->where('until', '>=', Carbon::now())->orderByDesc('updated_at')->get([
            'player',
            'last_coordinate',
            'updated_at',
            'until',
        ]);

        return response()->json(($found ? $found->toArray() : []));
    }

    public function help(Request $request)
    {
        $commmands = [
            'help'    => [
                'explanation' => 'Returns all the commands registered on this bot with explanation.',
                'aliases'     => [
                    'cmdlist',
                    'commands',
                    'bot',
                    'info',
                ],
                'arguments'   => [],
                'example'     => [
                    '',
                ],
            ],
            'purge'   => [
                'explanation' => 'Removes the 100 most recent messages in the channel you use the command in.',
                'aliases'     => [
                    'clean',
                    'clear',
                ],
                'arguments'   => [],
                'example'     => [
                    '',
                ],
            ],
            'players' => [
                'explanation' => 'Shows a list of players and their time connected on the coordinate of your choice.',
                'aliases'     => [
                    'player',
                ],
                'arguments'   => [
                    'COORDINATE:A1',
                    'REGION:eu',
                    'GAMEMODE:pvp',
                ],
                'example'     => [
                    '',
                ],
            ],
            'pop'     => [
                'explanation' => 'Show a list of the amount of players on and around the coordinate of your choice.',
                'aliases'     => [
                    'population',
                ],
                'arguments'   => [
                    'COORDINATE:A1',
                    'REGION:eu',
                    'GAMEMODE:pvp',
                ],
                'example'     => [
                    '',
                ],
            ],
            'grid'    => [
                'explanation' => 'Show a formatted table of the amount of players on and around the coordinate of your choice.',
                'aliases'     => [],
                'arguments'   => [
                    'COORDINATE:A1',
                    'REGION:eu',
                    'GAMEMODE:pvp',
                ],
                'example'     => [
                    '',
                ],
            ],
            'find'    => [
                'explanation' => 'Find a player (based on steam username). This currently only works for [EU PVP].',
                'aliases'     => [
                    'search',
                    'whereis',
                ],
                'arguments'   => [
                    'STEAMNAME:iShot',
                ],
                'example'     => [
                    '',
                ],
            ],
            'alert'   => [
                'explanation' => 'Adds a coordinate to the list of coordinates that trigger an alert when we think a boat of 2 or more people joined that coordinate.',
                'aliases'     => [
                    'prox',
                    'proximity',
                ],
                'arguments'   => [
                    'COORDINATE:A1',
                ],
                'example'     => [
                    '',
                ],
            ],
            'unalert' => [
                'explanation' => 'Removes a coordinate to the list of coordinates that trigger an alert when we think a boat of 2 or more people joined that coordinate.',
                'aliases'     => [
                    'unprox',
                    'unproximity',
                ],
                'arguments'   => [
                    'COORDINATE:A1',
                ],
                'example'     => [
                    '',
                ],
            ],
            'track'   => [
                'explanation' => 'Track a player for XXX minutes. Every time we detect the player changed coordinates the bot will post an alert',
                'aliases'     => [
                    'stalk',
                    'follow',
                ],
                'arguments'   => [
                    'MINUTES:120',
                    'USERNAME:iShot',
                ],
                'example'     => [
                    '',
                ],
            ],
            'untrack' => [
                'explanation' => 'Remove a player from the tracking list.',
                'aliases'     => [
                    'unstalk',
                    'unfollow',
                ],
                'arguments'   => [
                    'USERNAME:iShot',
                ],
                'example'     => [
                    '',
                ],
            ],
        ];

        return response()->json($commmands);
    }

    public function faq(Request $request)
    {
        $questions = [
            'Question' => [
                'answer' => 'answertest',
            ],
        ];

        return response()->json($questions);
    }
}
