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

    public function __construct($x = 1, $y = 1, $size = '15x15')
    {
        $this->x     = $x;
        $this->y     = $y;
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
                'text'      => chr(($x) + 64) . ($y),
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
                'text'      => chr($this->x + 64) . ($this->y),
                'direction' => 'center',
            ],
        ];
    }

}
