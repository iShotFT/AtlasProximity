<?php

namespace App\Http\Controllers;

use App\Boat;
use App\Classes\Coordinate;
use App\Events\TrackedPlayerLost;
use App\Events\TrackedPlayerMoved;
use App\Events\TrackedPlayerRefound;
use App\Events\TrackedServerBoat;
use App\Events\TrackExpired;
use App\Faq;
use App\Guild;
use App\Ping;
use App\PlayerPing;
use App\PlayerTrack;
use App\ProximityTrack;
use App\Update;
use Barryvdh\Snappy\Facades\SnappyImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * Check the registered proximity alerts and handle them
     *
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
    public static function proximity()
    {
        $coordinates_to_scan = ProximityTrack::select('coordinate')->distinct()->get();
        $proximity_tracks    = ProximityTrack::get();

        foreach ($coordinates_to_scan as $coordinate_to_scan) {
            // $coordinates_to_scan->coordinate
            // Get list of players from the previous scan
            if ($old_players = Ping::where('coordinates', $coordinate_to_scan->coordinate)->orderByDesc('created_at')->first()) {
                $previous_scanned_players = json_decode($old_players->info, true);
                if ($new_scanned_players = SourceQueryController::getPlayersOnCoordinate($coordinate_to_scan->coordinate, 'eu', 'pvp', true)['players']) {
                    // We only want to compare usernames
                    $previous_scanned_players = array_column($previous_scanned_players, 'Name');
                    $new_scanned_players      = array_column($new_scanned_players, 'Name');

                    $new_players_after_scan = array_diff($new_scanned_players, $previous_scanned_players);
                    //                    dd($new_players_after_scan);

                    if (is_array($new_players_after_scan) && count($new_players_after_scan) >= 2) {
                        // 3 or more new players joined in the past minute!!!
                        // Combine each username with the previous server they were spotted on

                        // Build an array with all r
                        $locations = [];
                        foreach ($new_players_after_scan as $username) {
                            if ($username !== '123' && !empty($username)) {
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
                            }
                        }

                        foreach ($locations as $location => $players) {
                            if (is_array($players) && count($players) >= 2) {
                                // Remove the current alert hit from the next warning
                                unset($locations[$location]);

                                // Only trigger a BOAT alert when the count of players is 2 or more
                                // Trigger 'Boat entered server XXX from XXX'
                                foreach ($proximity_tracks->where('coordinate', $coordinate_to_scan->coordinate) as $proximity_track) {
                                    $boat = Boat::create([
                                        'guild_id'   => $proximity_track->guild_id,
                                        'channel_id' => $proximity_track->channel_id,
                                        'coordinate' => $coordinate_to_scan->coordinate,
                                        'from'       => $location,
                                        'players'    => json_encode($players, true),
                                        'count'      => count($players),
                                    ]);

                                    event(new TrackedServerBoat($proximity_track, $players, $location, $boat));
                                }
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
     * Check and handle the registered player trackings
     *
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

    public function stats(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'period'   => 'required|string|in:day,week,month',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        $image = Cache::remember('stats_chart_for_' . $request->get('region') . $request->get('gamemode') . $request->get('period'), 10, function () use ($request) {
            $statsTable  = \Lava::DataTable();
            $currentHour = Carbon::now()->hour;

            if ($request->get('period') === 'day') {
                $statsTable->addDateColumn('Hour')->addNumberColumn('Players');

                $hours   = Ping::selectRaw('avg(players) as players, HOUR(created_at) as hour')->where('coordinates', $request->get('server'))->where('gamemode', $request->get('gamemode'))->where('region', $request->get('region'))->whereDate('created_at', '>=', Carbon::now()->subHours(24))->groupBy('hour')->pluck('players', 'hour')->toArray();
                $past_24 = array_merge(array_slice($hours, $currentHour, count($hours) - $currentHour), array_slice($hours, 0, $currentHour));

                foreach ($past_24 as $key => $value) {
                    //                dd($key, $value);
                    $statsTable->addRow([
                        Carbon::now()->subHours(24 - $key),
                        $value,
                    ]);
                }
            }

            if ($request->get('period') === 'week') {
                $statsTable->addDateColumn('Day')->addNumberColumn('Players');
            }

            if ($request->get('period') === 'month') {
                $statsTable->addDateColumn('Day')->addNumberColumn('Players');
            }

            $lineChart = \Lava::LineChart('LineChart', $statsTable, [
                'png'             => true,
                'curveType'       => 'function',
                'backgroundColor' => '#36393f',
                'chartArea'       => [
                    'width'  => '950',
                    'height' => '700',
                    'left'   => '50',
                ],
                'legend'          => 'none',
                'series'          => [
                    ['color' => 'white'],
                ],
                'width'           => 1000,
                'height'          => 750,
                'vAxis'           => [
                    'textStyle'     => [
                        'color' => 'white',
                    ],
                    'showTextEvery' => 5,
                ],
                'hAxis'           => [
                    'gridlines'     => [
                        'count' => 6,
                    ],
                    'baselineColor' => 'white',
                    'showTextEvery' => 1,
                    'textStyle'     => [
                        'color' => 'white',
                    ],
                ],
                'lineWidth'       => 10,
            ]);

            $lineChart->DateFormat([
                'pattern' => 'string',
            ]);

            $snappy_image = SnappyImage::loadView('chart', compact('lineChart'));
            // Options
            $snappy_image->setOption('enable-javascript', true);
            $snappy_image->setOption('javascript-delay', 1000);
            $filename = Carbon::now()->timestamp . '-' . $request->get('region') . '-' . $request->get('gamemode') . '.png';
            $snappy_image->save(storage_path() . '/app/public/images/chart/' . $filename);

            return url('/storage/images/chart/' . $filename);
        });

        return response()->json(['image' => $image]);
    }

    public function map(Request $request)
    {
        $request->validate([
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        // Cache::forget('servers_list_for_map' . $request->get('region') . $request->get('gamemode'));
        $image = Cache::remember('servers_list_for_map' . $request->get('region') . $request->get('gamemode'), 1, function () use ($request) {
            $region   = $request->get('region');
            $gamemode = $request->get('gamemode');

            $query_result = json_decode(json_encode(DB::select('SELECT id, region, gamemode, coordinates, players, info, created_at
                FROM pings
                WHERE id IN (
                    SELECT MAX(id)
                    FROM pings
                    WHERE gamemode = "' . $request->get('gamemode') . '" AND region = "' . $request->get('region') . '"
                    GROUP BY coordinates, region, gamemode
                )
                Order by coordinates'), true), true);
            $servers      = array_combine(array_column($query_result, 'coordinates'), $query_result);
            $max          = max(array_column($servers, 'players'));

            $grid = SourceQueryController::getAllServers($request->get('region'), $request->get('gamemode'));

            //        $snappy = App::make('snappy.image');
            // return view('snappy.map', compact('region', 'gamemode', 'servers', 'grid', 'max'))->render();
            $snappy_image = SnappyImage::loadView('snappy.map', compact('region', 'gamemode', 'servers', 'grid', 'max'));
            $filename     = Carbon::now()->timestamp . '-' . $region . '-' . $gamemode . '.png';
            $snappy_image->save(storage_path() . '/app/public/images/map/' . $filename);

            return url('/storage/images/map/' . $filename);
        });


        // return $image;

        return response()->json(['image' => $image]);
    }

    public function faq(Request $request)
    {
        $faqs = Faq::orderByDesc('created_at')->get([
            'question',
            'answer',
            'created_at',
        ]);

        return response()->json($faqs->toArray());
    }

    public function version(Request $request)
    {
        $latest_update = Update::orderByDesc('created_at')->first();

        return response()->json([
            'version'    => $latest_update->full_version,
            'changes'    => $latest_update->changes,
            'created_at' => $latest_update->created_at->timestamp,
        ]);
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

    public function findBoat(Request $request)
    {
        $request->validate([
            'boatid'  => 'required|integer|min:1',
            'guildid' => 'required',
        ]);

        // Get boat players (only find boats from your own guild, no spying)
        if ($boat = Boat::where('id', $request->get('boatid'))->where('guild_id', $request->get('guildid'))->first()) {
            $players = json_decode($boat->players, true);
            $return  = collect();
            if (is_array($players) && count($players) >= 2) {
                foreach ($players as $player) {
                    $return->push(PlayerPing::where('player', '=', $player)->orderByDesc('updated_at')->limit(1)->get([
                        'player',
                        'coordinates',
                        'updated_at',
                    ]));
                }

                return response()->json($return->toArray());
            } else {
                return response()->json(['message' => 'Boat with ID ' . $request->get('boatid') . ' not found in the list of boats tracked by your Discord.'], 404);
            }
        }
    }

    public function guildAdd(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'id'   => 'required',
        ]);

        Guild::updateOrCreate([
            'guild_id' => $request->get('id'),
        ], [
            'name'       => $request->get('name'),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
        ]);
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

    public function trackRemoveAll(Request $request)
    {
        $request->validate([
            //            'username' => 'required|string|min:2',
            'guildid'   => 'required',
            'channelid' => 'required',
        ]);

        PlayerTrack::where([
            //            'player'   => $request->get('username'),
            'guild_id'   => $request->get('guildid'),
            'channel_id' => $request->get('channelid'),
        ])->delete();
    }

    public function proximityRemoveAll(Request $request)
    {
        $request->validate([
            'guildid'   => 'required',
            'channelid' => 'required',
        ]);

        ProximityTrack::where([
            'guild_id'   => $request->get('guildid'),
            'channel_id' => $request->get('channelid'),
        ])->delete();
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

    public function guildRemove(Request $request)
    {
        $request->validate([
            //            'name' => 'required|string',
            'id' => 'required',
        ]);

        if ($guild = Guild::where('guild_id', $request->get('id'))->first()) {
            $guild->delete();
        };
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
            'help'       => [
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
            'version'    => [
                'explanation' => 'Find out what version the bot is currently on. This includes the latest changes.',
                'aliases'     => [
                    'v',
                ],
                'arguments'   => [],
                'example'     => [
                    '',
                ],
            ],
            'ask'        => [
                'explanation' => 'Send a message to the creator / owner of this bot. This can be used to send feedback, ask for help, etc.',
                'aliases'     => [
                    'contact',
                    'question',
                    'feedback',
                ],
                'arguments'   => [
                    'MESSAGE',
                ],
                'example'     => [
                    '',
                ],
            ],
            'purge'      => [
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
            'map'        => [
                'explanation' => 'Generate and show an image of the map with the current population of each server.',
                'aliases'     => [
                    'world',
                ],
                'arguments'   => [
                    'REGION:eu',
                    'GAMEMODE:pvp',
                ],
                'example'     => [
                    '',
                ],
            ],
            'players'    => [
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
            'pop'        => [
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
            'grid'       => [
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
            'find'       => [
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
            'alert'      => [
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
            'unalert'    => [
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
            'unalertall' => [
                'explanation' => 'Removes all active tracking from this channel (needs ADMINISTRATOR or MANAGE_MESSAGES permission on Discord server).',
                'aliases'     => [
                    'unproxall',
                    'unproximityall',
                ],
                'arguments'   => [],
                'example'     => [
                    '',
                ],
            ],
            'track'      => [
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
            'untrack'    => [
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
            'untrackall' => [
                'explanation' => 'Removes all active proximity alerts from this channel (needs ADMINISTRATOR or MANAGE_MESSAGES permission on Discord server).',
                'aliases'     => [
                    'unstalk',
                    'unfollow',
                ],
                'arguments'   => [],
                'example'     => [
                    '',
                ],
            ],
        ];

        return response()->json($commmands);
    }
}
