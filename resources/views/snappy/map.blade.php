<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Atlas Map</title>

        <style>
            /*table {*/
            /*border: 1px solid black;*/
            /*}*/

            table {
                background-image: url('/storage/atlas_map.png');
                background-size: cover; /* <------ */
                background-repeat: no-repeat;
                background-position: center center; /* optional, center the image */
                width: 1000px;
                height: 1000px;
            }

            table td, table th {
                text-align: center;
                border: 1px solid white;
                box-sizing: border-box;
                width: 65px;
                height: 65px;
                /*height: 0;*/
            }

            table tr:nth-child(even) {
                /*background-color: #f2f2f2;*/
            }
        </style>
    </head>
    <body>
        <table BORDER=0 CELLSPACING=0 CELLPADDING=0>
            @for($y = 1; $y <= count($grid); $y++)
                <tr>
                    @for($x = 1; $x <= count($grid[chr($y + 64)]); $x++)
                        @php($x_text = chr($x + 64))
                        <td style="background-color: {!! $servers[$x_text . $y]['players'] !== false ? \App\Http\Controllers\ApiController::percent2Color($servers[$x_text . $y]['players'], 255, $max) : 'ffffff' !!};">
                            <b>{{ $x_text . $y }}</b><br/>
                            {{ $servers[$x_text . $y]['players']  !== false ? $servers[$x_text . $y]['players'] : '?' }}
                        </td>
                    @endfor
                </tr>
            @endfor
        </table>
    </body>
</html>