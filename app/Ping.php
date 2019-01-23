<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Ping
 *
 * @property int $id
 * @property string $ip
 * @property string $port
 * @property string $region
 * @property string $gamemode
 * @property string $coordinates
 * @property int $online
 * @property int $players
 * @property string|null $info
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereGamemode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping wherePlayers($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ping whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ping extends Model
{
    protected $guarded = [];


}
