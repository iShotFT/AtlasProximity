<?php

namespace App\Http\Controllers;

use App\Classes\Coordinate;
use App\Ping;
use App\PlayerPing;
use App\ProximityTrack;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use xPaw\SourceQuery\Exception\TimeoutException;
use xPaw\SourceQuery\SourceQuery;

class SourceQueryController extends Controller
{
    protected static $server_timeout = 1;
    protected static $server_engine = SourceQuery::SOURCE;

    /**
     * @param string $coordinate
     * @param string $region
     * @param string $gamemode
     *
     * @return array
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
    public static function getPlayersOnCoordinateWithSurrounding($coordinate = 'A1', $region = 'eu', $gamemode = 'pvp')
    {
        $players = array();


        // First get the center server players
        $information              = self::getPlayersOnCoordinate($coordinate, $region, $gamemode);
        $information['direction'] = '';
        $information['unicode']   = '00B7';
        $players[$coordinate]     = $information;

        // Get players for all surrounding servers
        $center = new Coordinate($coordinate);
        foreach ($center->getSurrounding() as $coordinate) {
            // x
            // y
            // text
            // direction
            $information                  = self::getPlayersOnCoordinate($coordinate['text'], $region, $gamemode);
            $information['direction']     = $coordinate['direction'];
            $information['unicode']       = $coordinate['unicode'];
            $players[$coordinate['text']] = $information;
        }

        return $players;
    }

    /**
     * @param string $ip
     * @param string $port
     *
     * @return array|bool|string
     * @throws \xPaw\SourceQuery\Exception\InvalidArgumentException
     * @throws \xPaw\SourceQuery\Exception\InvalidPacketException
     * @throws \xPaw\SourceQuery\Exception\TimeoutException
     */
    public static function getPlayersOnCoordinate($coordinate = 'A1', $region = 'eu', $gamemode = 'pvp', $skip_cache = false)
    {
        $return = '';

        // Get the IP for this server
        if (Cache::has('getPlayersOnCoordinate' . $coordinate . $region . $gamemode) && $skip_cache === false) {
            $return                           = Cache::get('getPlayersOnCoordinate' . $coordinate . $region . $gamemode);
            $return['data']['type']           = 'redis';
            $return['data']['age']['seconds'] = Carbon::now()->timestamp - $return['data']['age']['timestamp'];
        } else {
            list ($ip, $port) = array_values(self::getServerIp($coordinate, $region, $gamemode));
            // First check if server wasn't polled already in the past minute
            if (($ping = Ping::whereIp($ip)->wherePort((string)$port)->whereOnline(1)->whereNotNull('players')->where('created_at', '>=', Carbon::now()->subMinutes(config('atlas.settings.cache.lifetime', 1)))->first()) && $skip_cache === false) {
                $players = json_decode($ping->info, true);
                $data    = [
                    'type' => 'database',
                    'age'  => [
                        'timestamp' => $ping->created_at->timestamp,
                        'seconds'   => Carbon::now()->timestamp - $ping->created_at->timestamp,
                    ],
                ];
            } else {
                // No database record found younger than a minute. Pull new information
                try {
                    $Query = new SourceQuery();
                    $Query->Connect($ip, $port, self::$server_timeout, self::$server_engine);
                    $players = $Query->GetPlayers();

                    $data = [
                        'type' => 'live',
                        'age'  => [
                            'timestamp' => Carbon::now()->timestamp,
                            'seconds'   => 0,
                        ],
                    ];

                    // Store pulled information into the DB
                    Ping::create([
                        'ip'          => $ip,
                        'port'        => $port,
                        'region'      => $region,
                        'gamemode'    => $gamemode,
                        'coordinates' => $coordinate,
                        'online'      => 1,
                        'players'     => (is_array($players) ? sizeof($players) : null),
                        'info'        => json_encode($players, true),
                    ]);

                    // Store the players in the database
                    if (is_array($players) && count($players)) {
                        $to_be_updated = array();
                        foreach ($players as $player) {
                            if (!empty($player['Name']) && $player['Name'] !== "123") {
                                $playerping = PlayerPing::firstOrNew([
                                    'player'      => $player['Name'],
                                    'region'      => $region,
                                    'gamemode'    => $gamemode,
                                    'coordinates' => $coordinate,
                                ], [
                                    'ip'   => $ip,
                                    'port' => $port,
                                ]);

                                if ($playerping->id) {
                                    array_push($to_be_updated, $playerping->id);
                                } else {
                                    $playerping->save();
                                }
                            }
                        }

                        $now = Carbon::now();
                        if (count($to_be_updated)) {
                            PlayerPing::whereIn('id', $to_be_updated)->update(['updated_at' => $now]);
                        }
                    }
                } catch (TimeoutException $e) {
                    // Failed to poll the server. Offline?
                    Ping::create([
                        'ip'          => $ip,
                        'port'        => $port,
                        'region'      => $region,
                        'gamemode'    => $gamemode,
                        'coordinates' => $coordinate,
                        'online'      => 0,
                        'players'     => null,
                        'info'        => null,
                    ]);

                    $players = null;
                }
                finally {
                    $Query->Disconnect();
                }
            }

            $return = [
                'players' => $players,
                'count'   => (is_array($players) ? count($players) : 0),
                'data'    => $data,
            ];

            // Save in cache for next usage
            Cache::put('getPlayersOnCoordinate' . $coordinate . $region . $gamemode, $return, 0.25);
        }

        return $return;
    }

    public static function getServerIp($coordinate = 'A1', $region = 'eu', $gamemode = 'pvp')
    {
        $servers = config('atlas.servers.' . $region . '.' . $gamemode, null);

        if ($servers) {
            // Only build the server list once every hour.
            $return = self::getAllServers($region, $gamemode);

            // Calculate what entry of the server matrix we need
            list($coord_x, $coord_y) = Coordinate::textToSplit($coordinate); // ['B', '4']

            return $return[$coord_x][$coord_y];
        } else {
            abort(401, 'Configuration for this region (' . $region . ') / gamemode (' . $gamemode . ') missing!');
        }
    }

    public static function getAllServers($region = 'eu', $gamemode = 'pvp')
    {
        $servers = config('atlas.servers.' . $region . '.' . $gamemode, null);

        $return = Cache::remember('servers_list' . $region . $gamemode, 60, function () use ($servers) {
            $max_x = explode('x', $servers['size'])[0];
            $max_y = explode('x', $servers['size'])[1];

            $servers_list = array();
            foreach ($servers['ip'] as $ip) {
                foreach ($servers['port'] as $port) {
                    array_push($servers_list, [
                        'ip'   => $ip,
                        'port' => $port,
                    ]);
                }
            }

            $return    = array();
            $iteration = 0;
            for ($x = 1; $x <= $max_x; $x++) {
                $character          = chr($x + 64);
                $return[$character] = array();
                for ($y = 1; $y <= $max_y; $y++) {
                    $return[$character][$y] = $servers_list[$iteration];
                    $iteration++;
                }
            }

            return $return;
        });

        return $return;
    }

    public function test(Request $request)
    {
        //        dd(self::getAllPlayersAllServers('na', 'pvp'));
        dd(self::getAllServers('eu', 'pve'));
        $Query = new SourceQuery();
        $Query->Connect('46.251.238.58', 57555, self::$server_timeout, self::$server_engine);
        $players = $Query->GetRules();
        dd($players);

        //        Cache::forget('getPlayersOnCoordinateB4eupvp');
        dd(SourceQueryController::getAllPlayersAllServers());
        $proximity = ProximityTrack::updateOrCreate([
            //            $table->string('guild_id');
            //        $table->string('channel_id');
            //        $table->string('coordinate');
            'guild_id'   => '123456',
            'channel_id' => '123456',
            'coordinate' => 'B4',
        ]);
        dd(ApiController::proximity());

        //        $start = Carbon::now()->timestamp;
        //        self::getAllPlayersAllServers();
        //        dd('end', Carbon::now()->timestamp - $start);
    }

    public static function getAllPlayersAllServers($region = 'eu', $gamemode = 'pvp')
    {
        foreach (self::getAllServers($region, $gamemode) as $x => $servers) {
            foreach ($servers as $y => $server) {
                self::getPlayersOnCoordinate($x . $y, $region, $gamemode);
            }
        };
    }
}
