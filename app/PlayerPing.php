<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PlayerPing
 *
 * @property int $id
 * @property string $ip
 * @property string $player
 * @property string $port
 * @property string $region
 * @property string $gamemode
 * @property string $coordinates
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing whereCoordinates($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing whereGamemode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing wherePlayer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerPing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlayerPing extends Model
{
    protected $guarded = [];
}
