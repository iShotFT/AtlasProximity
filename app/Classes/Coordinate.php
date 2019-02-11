<?php

namespace App\Classes;

use Illuminate\Support\Facades\Cache;

class Coordinate
{
    protected static $cardinalDegrees = [
        'North'           => [
            348.75,
            360,
        ],
        'North2'          => [
            0,
            11.25,
        ],
        'North-northeast' => [
            11.25,
            33.75,
        ],
        'Northeast'       => [
            33.75,
            56.25,
        ],
        'East-northeast'  => [
            56.25,
            78.75,
        ],
        'East'            => [
            78.75,
            101.25,
        ],
        'East-southeast'  => [
            101.25,
            123.75,
        ],
        'Southeast'       => [
            123.75,
            146.25,
        ],
        'South-southeast' => [
            146.25,
            168.75,
        ],
        'South'           => [
            168.75,
            191.25,
        ],
        'South-southwest' => [
            191.25,
            213.75,
        ],
        'Southwest'       => [
            213.75,
            236.25,
        ],
        'West-southwest'  => [
            236.25,
            258.75,
        ],
        'West'            => [
            258.75,
            281.25,
        ],
        'West-northwest'  => [
            281.25,
            303.75,
        ],
        'Northwest'       => [
            303.75,
            326.25,
        ],
        'North-northwest' => [
            326.25,
            348.75,
        ],
    ];
    protected $x;
    protected $x_max;
    protected $x_min = 1;
    protected $y;
    protected $y_max;
    protected $y_min = 1;
    protected $surroundings = [
        [
            -1,
            0,
            'west',
            '2B05',
        ],
        [
            -1,
            -1,
            'northwest',
            '2196',
        ],
        [
            0,
            -1,
            'north',
            '2B06',
        ],
        [
            1,
            -1,
            'northeast',
            '2197',
        ],
        [
            1,
            0,
            'east',
            '27A1',
        ],
        [
            1,
            1,
            'southeast',
            '2198',
        ],
        [
            0,
            1,
            'south',
            '2B07',
        ],
        [
            -1,
            1,
            'southwest',
            '2199',
        ],
    ];

    /**
     * Coordinate constructor.
     *
     * @param string $input
     * @param string $size
     */
    public function __construct($input = 'A1', $size = '15x15')
    {
        if (is_array($input)) {
            list($this->x, $this->y) = $input;
        } else {
            list($this->x, $this->y) = self::textToXY($input);
        }

        $this->x_max = explode('x', $size)[0];
        $this->y_max = explode('x', $size)[1];
    }

    /**
     * @param string $input
     *
     * @return array
     */
    static function textToXY($input = 'A1')
    {
        $server_x = substr($input, 0, 1); // A
        $server_y = substr($input, 1, strlen($input) - 1); // 15

        $server_x = ord($server_x) - 64;

        return [
            $server_x,
            (int)$server_y,
        ];
    }

    /**
     * @param string $input
     *
     * @return array
     */
    static function textToSplit($input = 'A1')
    {
        return [
            substr($input, 0, 1),
            (int)substr($input, 1, strlen($input) - 1),
        ];
    }

    /**
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     *
     * @return mixed
     */
    public static function cardinalDirectionBetween($x1, $y1, $x2, $y2)
    {
        // https://gist.github.com/smallindine/d227743c28418f3426ed36b8969ded1a -> radial to cardinal
        // rad2deg(atan2($y2-$y1,$x2-$x1));
        // 360 + rad2deg(atan2(3-4,2-3));

        $y1 = 15 - $y1;
        $y2 = 15 - $y2;

        $rad    = rad2deg(atan2($y2 - $y1, $x2 - $x1));
        $degree = 90 - ($rad > 90 ? -($rad) : $rad);

        foreach (self::$cardinalDegrees as $dir => $angles) {
            if ($degree >= $angles[0] && $degree < $angles[1]) {
                $cardinal = str_replace("2", "", $dir);
            }
        }

        return $cardinal;
    }

    /**
     * @return array
     */
    public function getSurrounding()
    {
        $target = Cache::remember('getSurrounding' . $this->x . $this->y, 1440, function () {
            $target = array_map(function ($movement) {
                $x = $this->x + $movement[0];
                $x = (($x < $this->x_min) ? $this->x_max + ($x % $this->x_max) : ($x > $this->x_max ? ($x % $this->x_max) : $x));

                $y = $this->y + $movement[1];
                $y = (($y < $this->y_min) ? $this->y_max + ($y % $this->y_max) : ($y > $this->y_max ? ($y % $this->y_max) : $y));

                //$x = $movement[0] + ((($movement[0] + $this->x) < $this->x_min) ? $this->x_max + ($this->x % $this->x_max) : $this->x % $this->x_max);
                // $y = $movement[1] + ((($movement[1] + $this->y) < $this->y_min) ? $this->y_max + ($this->y % $this->y_max) : $this->y % $this->y_max);

                return [
                    'x'         => $x,
                    'y'         => $y,
                    'text'      => self::xyToText($x, $y),
                    'direction' => $movement[2],
                    'unicode'   => $movement[3],
                ];
            }, $this->surroundings);

            return $target;
        });

        return $target;
    }

    /**
     * @param int $x
     * @param int $y
     *
     * @return string
     */
    static function xyToText($x = 1, $y = 1)
    {
        return chr(($x) + 64) . ($y);
    }

    /**
     * @return array
     */
    public function getCenter()
    {
        return [
            'x'         => $this->x,
            'y'         => $this->y,
            'text'      => self::xyToText($this->x, $this->y),
            'direction' => 'center',
            'unicode'   => '2022',
        ];
    }

    /**
     * @param string $coordinate
     * @param string $region
     * @param string $gamemode
     *
     * @return mixed
     */
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

    /**
     * @param string $region
     * @param string $gamemode
     *
     * @return mixed
     */
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

    public static function getCoordinateFromIpPort($ip, $port)
    {
        foreach (config('atlas.servers') as $region => $gamemodes) {
            foreach ($gamemodes as $gamemode => $info) {
                $servers = Coordinate::getAllServers($region, $gamemode);
                foreach ($servers as $x => $row) {
                    foreach ($row as $y => $server) {
                        if ($server['ip'] === $ip && $server['port'] === (int)$port) {
                            return $x . $y;
                        }
                    }
                }
            }
        }
    }
}