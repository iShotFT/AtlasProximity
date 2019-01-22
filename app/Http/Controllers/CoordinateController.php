<?php

namespace App\Http\Controllers;

class CoordinateController extends Controller
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
        ],
        [
            -1,
            -1,
            'northwest',
        ],
        [
            0,
            -1,
            'north',
        ],
        [
            1,
            -1,
            'northeast',
        ],
        [
            1,
            0,
            'east',
        ],
        [
            1,
            1,
            'southeast',
        ],
        [
            0,
            1,
            'south',
        ],
        [
            -1,
            1,
            'southwest',
        ],
    ];

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

    public function getSurrounding()
    {
        $target = array_map(function ($movement) {
            $x = $movement[0] + ((($movement[0] + $this->x) < $this->x_min) ? $this->x_max + ($this->x % $this->x_max) : $this->x % $this->x_max);
            $y = $movement[1] + ((($movement[1] + $this->y) < $this->y_min) ? $this->y_max + ($this->y % $this->y_max) : $this->y % $this->y_max);

            return [
                'x'         => $x,
                'y'         => $y,
                'text'      => self::xyToText($x, $y),
                'direction' => $movement[2],
            ];
        }, $this->surroundings);

        return $target + $this->getCenter();
    }

    public function getCenter()
    {
        return [
            [
                'x'         => $this->x,
                'y'         => $this->y,
                'text'      => self::xyToText($this->x, $this->y),
                'direction' => 'center',
            ],
        ];
    }

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

    static function xyToText($x = 1, $y = 1)
    {

        return chr(($x) + 64) . ($y);
    }

}
