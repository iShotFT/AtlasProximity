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
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    protected $settings = [
        'required' => [
            'region'   => [
                'eu',
                'na',
            ],
            'gamemode' => [
                'pvp',
                //                'pve',
            ],
        ],
        'optional' => [

        ],
    ];

    public function settings(Request $request)
    {
        $request->validate([
            'guildid'   => 'required|exists:guilds,guild_id',
            'parameter' => 'sometimes|string',
            'value'     => 'sometimes|string',
        ]);

        // Get guild
        $guild          = Guild::where('guild_id', $request->get('guildid'))->first();
        $returnsettings = [];

        if ($request->has('parameter')) {
            // Find the setting and return
            foreach ($this->settings as $type => $items) {
                foreach ($items as $parameter => $possible) {
                    if ($parameter === $request->get('parameter')) {
                        $returnsettings['returntype'] = 'get';

                        if ($request->has('value')) {
                            if (is_array($possible) && !in_array($request->get('value'), $possible)) {
                                // The value they're trying to insert is not one of the possible values
                                return response()->json(['message' => 'The value `' . $request->get('value') . '` is not a valid value for the `' . $request->get('parameter') . '` setting. Use one of the valid values: `' . implode('`, `', $possible) . '`'], 400);
                            }

                            // Set the setting and return
                            $guild->update([
                                $parameter => $request->get('value'),
                            ]);

                            $returnsettings['returntype'] = 'set';
                            // Todo: reset all trackings related to this guild
                        }

                        $returnsettings['parameter'] = $request->get('parameter');
                        $returnsettings['type']      = $type;
                        $returnsettings['options']   = join('|', $possible ?? []);
                        $returnsettings['current']   = $guild->$parameter ?? 'NOT CURRENTLY SET';

                    }
                }
            }

            if (!empty($returnsettings)) {
                return response()->json($returnsettings);
            } else {
                return response()->json(['message' => 'Something went wrong, make sure to use `!settings` to see the possible parameters and values.'], 400);
            }
        }

        // List all settings and their values
        foreach ($this->settings['required'] as $parameter => $possible) {
            $returnsettings['required'][$parameter] = [
                'options' => join('|', $possible ?? []),
                'current' => $guild->$parameter ?? 'NOT CURRENTLY SET',
            ];
        }
        $returnsettings['returntype'] = 'all';

        return response()->json($returnsettings);
    }

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
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        $found = PlayerPing::where('player', $request->get('username'))->where('region', $request->get('region'))->where('gamemode', $request->get('gamemode'))->orderByDesc('updated_at')->limit(5)->get([
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
            'boatid'   => 'required|integer|min:1',
            'guildid'  => 'required',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        // Get boat players (only find boats from your own guild, no spying)
        if ($boat = Boat::where('id', $request->get('boatid'))->where('guild_id', $request->get('guildid'))->where('region', $request->get('region'))->where('gamemode', $request->get('gamemode'))->first()) {
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

    public function guildsAdd(Request $request)
    {
        $data = json_decode(json_encode($request->get('data'), true), true);

        $count = 0;
        foreach ($data as $server) {
            // $server['name']
            // $server['guildid'];
            // $server['users'];
            $request->request->add(['name' => $server['name']]);
            $request->request->add(['guildid' => $server['guildid']]);
            $request->request->add(['users' => $server['users']]);

            $this->guildAdd($request);
            $count++;
        }

        return response()->json(['message' => 'All ' . $count . ' servers added or updated in the database']);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function guildAdd(Request $request)
    {
        $request->validate([
            'name'    => 'required|string',
            'guildid' => 'required',
        ]);

        $guild = Guild::updateOrCreate([
            'guild_id' => $request->get('guildid'),
        ], [
            'users'      => $request->get('users') ?? 0,
            'name'       => $request->get('name'),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
        ]);

        // Check if guild has a region & gamemode set
        if (is_null($guild->region) || is_null($guild->gamemode)) {
            return response()->json([
                'missingsettings' => true,
                'message'         => 'Server ' . $guild->name . ' added / updated but is missing required information!',
            ]);
        } else {
            return response()->json([
                'missingsettings' => false,
                'message'         => 'Server ' . $guild->name . ' added / updated correctly.',
            ]);
        }
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
            'region'     => 'required|string|size:2',
            'gamemode'   => 'required|string|size:3',
        ]);

        // Refresh the data
        SourceQueryController::getPlayersOnCoordinateWithSurrounding($request->get('coordinate'), $request->get('region'), $request->get('gamemode'));

        ProximityTrack::updateOrCreate([
            'coordinate' => $request->get('coordinate'),
            'guild_id'   => $request->get('guildid'),
            'channel_id' => $request->get('channelid'),
            'region'     => $request->get('region'),
            'gamemode'   => $request->get('gamemode'),
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
            'region'    => 'required|string|size:2',
            'gamemode'  => 'required|string|size:3',
        ]);

        // First make sure we can find the player in the tracking DB
        if ($last_ping = PlayerPing::where('player', $request->get('username'))->where('updated_at', '>=', Carbon::now()->subMinutes(10))->orderByDesc('updated_at')->first()) {
            // Will overwrite a track if one already exists for this guild and player
            $playertrack = PlayerTrack::updateOrCreate([
                'guild_id' => $request->get('guildid'),
                'player'   => $request->get('username'),
                'region'   => $request->get('region'),
                'gamemode' => $request->get('gamemode'),
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
            'guildid'  => 'required|integer',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        $found = ProximityTrack::where('guild_id', $request->get('guildid'))->where('region', $request->get('region'))->where('gamemode', $request->get('gamemode'))->get([
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
            'guildid'  => 'required|integer',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        $found = PlayerTrack::where('guild_id', $request->get('guildid'))->where('until', '>=', Carbon::now())->where('region', $request->get('region'))->where('gamemode', $request->get('gamemode'))->orderByDesc('updated_at')->get([
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
        return response()->json(['url' => url('/docs')]);
    }
}
