<?php

namespace App\Classes;

class Coordinate
{
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

    static function textToSplit($input = 'A1')
    {
        return [
            substr($input, 0, 1),
            (int)substr($input, 1, strlen($input) - 1),
        ];
    }

    /**
     * @return array
     */
    public function getSurrounding()
    {
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
}