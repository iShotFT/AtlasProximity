<?php

namespace App\Http\Controllers;

use App\Classes\Coordinate;
use App\Ping;
use App\PlayerPing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use xPaw\SourceQuery\Exception\TimeoutException;
use xPaw\SourceQuery\SourceQuery;

class SourceQueryController extends Controller
{
    protected static $server_timeout = 1;
    protected static $server_engine = SourceQuery::SOURCE;

    public function test(Request $request)
    {
        $server = 'A1';
        if ($request->has('server')) {
            $server = strtoupper($request->get('server'));
        }

        dd($this->getCoordinatePlayersWithSurrounding($server));

        $coord = new Coordinate($server);
    }

    public static function getCoordinatePlayersWithSurrounding($coordinate = 'A1', $region = 'eu', $gamemode = 'pvp')
    {
        $players = array();
        // First get the center server players
        $information              = self::getCoordinatePlayers($coordinate, $region, $gamemode);
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
            $information                  = self::getCoordinatePlayers($coordinate['text'], $region, $gamemode);
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
    public static function getCoordinatePlayers($coordinate = 'A1', $region = 'eu', $gamemode = 'pvp')
    {
        $Query  = new SourceQuery();
        $return = '';

        // Get the IP for this server
        list ($ip, $port) = array_values(self::getServerIp($coordinate, $region, $gamemode));

        // First check if server wasn't polled already in the past minute
        if ($ping = Ping::whereIp($ip)->wherePort((string)$port)->whereOnline(1)->whereNotNull('players')->where('created_at', '>=', Carbon::now()->subMinute())->first()) {
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
                    foreach ($players as $player) {
                        PlayerPing::create([
                            'ip'          => $ip,
                            'player'      => $player['Name'],
                            'port'        => $port,
                            'region'      => $region,
                            'gamemode'    => $gamemode,
                            'coordinates' => $coordinate,
                        ]);
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

        return [
            'players' => $players,
            'count'   => (is_array($players) ? count($players) : 0),
            'data'    => $data,
        ];
    }

    public static function getServerIp($coordinate = 'A1', $region = 'eu', $gamemode = 'pvp')
    {
        $servers = config('atlas.servers.' . $region . '.' . $gamemode, null);

        if ($servers) {
            // Only build the server list once every hour.
            $return = Cache::remember('servers_list', 60, function () use ($servers) {
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

            // Calculate what entry of the server matrix we need
            list($coord_x, $coord_y) = Coordinate::textToSplit($coordinate); // ['B', '4']

            return $return[$coord_x][$coord_y];
        } else {
            abort(401, 'Configuration for this region (' . $region . ') / gamemode (' . $gamemode . ') missing!');
        }
    }
}
