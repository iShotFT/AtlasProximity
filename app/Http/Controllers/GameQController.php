<?php

namespace App\Http\Controllers;

use App\Classes\Coordinate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class GameQController extends Controller
{
    /**
     * @throws \Exception
     */
    public function test()
    {
        //        $region   = 'eu';
        //        $gamemode = 'pvp';
        //
        //        $GameQ   = new GameQ();
        //        $servers = Coordinate::getAllServers($region, $gamemode);
        //        foreach ($servers as $x => $row) {
        //            foreach ($row as $y => $server) {
        //                $GameQ->addServer([
        //                    'type'    => 'source',
        //                    'host'    => $server['ip'] . ':' . $server['port'],
        //                    'options' => [
        //                        'query_port' => $server['port'],
        //                    ],
        //                ]);
        //            }
        //        }
        //
        //        // Options
        //        //        $GameQ->setOption('write_wait', 20000);
        //        $GameQ->setOption('timeout', 3);
        //
        //        $results = $GameQ->process();
        //        dd(array_column($results, 'players'));
        return self::getPlayersOnCoordinate('B4', 'eu', 'pvp');
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
        $server_info = Coordinate::getServerIp($coordinate, $region, $gamemode);
        $cache_name  = 'serverInfo' . $server_info['ip'] . ':' . $server_info['port'];

        if ($info = Cache::pull($cache_name)) {
            return $info['players'];
        } else {
            // Rebuild cache
            Artisan::call('atlascctv:scanregion', [
                'region'   => $region,
                'gamemode' => $gamemode,
            ]);

            // Restart function
            return self::getPlayersOnCoordinate($coordinate, $region, $gamemode);
        };
    }

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
}
