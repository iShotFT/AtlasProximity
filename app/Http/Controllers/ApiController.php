<?php

namespace App\Http\Controllers;

use App\Boat;
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'period'   => 'required|string|in:day,week,month',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        $image = Cache::remember('stats_chart_for_' . $request->get('server') . $request->get('region') . $request->get('gamemode') . $request->get('period'), 10, function () use ($request) {
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
                'backgroundColor' => '#36393E',
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function faq(Request $request)
    {
        $faqs = Faq::orderByDesc('created_at')->get([
            'question',
            'answer',
            'created_at',
        ]);

        return response()->json($faqs->toArray());
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function version(Request $request)
    {
        $latest_update = Update::orderByDesc('created_at')->first();

        return response()->json([
            'version'    => $latest_update->full_version,
            'changes'    => $latest_update->changes,
            'created_at' => $latest_update->created_at->timestamp,
        ]);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
    public function players(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        return response()->json(SourceQueryController::getPlayersOnCoordinate($request->get('server'), $request->get('region'), $request->get('gamemode')));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
    public function population(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        return response()->json(SourceQueryController::getPlayersOnCoordinateWithSurrounding($request->get('server'), $request->get('region'), $request->get('gamemode')));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     */
    public function trackRemoveAll(Request $request)
    {
        $request->validate([
            'guildid'   => 'required',
            'channelid' => 'required',
        ]);

        PlayerTrack::where([
            'guild_id'   => $request->get('guildid'),
            'channel_id' => $request->get('channelid'),
        ])->delete();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     */
    public function trackRemove(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:2',
            'guildid'  => 'required',
        ]);

        PlayerTrack::where([
            'player'   => $request->get('username'),
            'guild_id' => $request->get('guildid'),
        ])->delete();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
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
