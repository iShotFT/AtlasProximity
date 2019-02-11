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
            list ($ip, $port) = array_values(Coordinate::getServerIp($coordinate, $region, $gamemode));
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
}
