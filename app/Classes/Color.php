<?php

namespace App\Classes;

class Color
{
    /**
     * Convert value to a color between green and red as HEX
     *
     * @param        $value
     * @param int    $brightness
     * @param int    $max
     * @param int    $min
     * @param string $thirdColorHex
     *
     * @return string
     */
    public static function percent2Color($value, $brightness = 255, $max = 75, $min = 1, $thirdColorHex = '00')
    {
        if ($value < $min) {
            return self::hex2rgba('ffffff', 0.5);
        }

        // Calculate first and second color (Inverse relationship)
        $second = (1 - ($value / $max)) * $brightness;
        $first  = ($value / $max) * $brightness;

        // Find the influence of the middle color (yellow if 1st and 2nd are red and green)
        $diff      = abs($first - $second);
        $influence = ($brightness - $diff) / 2;
        $first     = intval($first + $influence);
        $second    = intval($second + $influence);

        // Convert to HEX, format and return
        $firstHex  = str_pad(dechex($first), 2, 0, STR_PAD_LEFT);
        $secondHex = str_pad(dechex($second), 2, 0, STR_PAD_LEFT);

        return self::hex2rgba($firstHex . $secondHex . $thirdColorHex, 0.5);

        // alternatives:
        // return $thirdColorHex . $firstHex . $secondHex;
        // return $firstHex . $thirdColorHex . $secondHex;

    }

    /**
     * Convert HEX color to an RGBA color with a certain opacity
     *
     * @param      $color
     * @param bool $opacity
     *
     * @return string
     */
    public static function hex2rgba($color, $opacity = false)
    {

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if (empty($color)) {
            return $default;
        }

        //Sanitize $color if "#" is provided
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        //Check if color has 6 or 3 characters and get values
        if (strlen($color) == 6) {
            $hex = array(
                $color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5],
            );
        } elseif (strlen($color) == 3) {
            $hex = array(
                $color[0] . $color[0],
                $color[1] . $color[1],
                $color[2] . $color[2],
            );
        } else {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if ($opacity) {
            if (abs($opacity) > 1) {
                $opacity = 1.0;
            }
            $output = 'rgba(' . implode(",", $rgb) . ',' . $opacity . ')';
        } else {
            $output = 'rgb(' . implode(",", $rgb) . ')';
        }

        //Return rgb(a) color string
        return $output;
    }
}