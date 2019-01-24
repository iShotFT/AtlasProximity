<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\PlayerTrack
 *
 * @property int                             $id
 * @property int                             $guild_id
 * @property int                             $channel_id
 * @property string                          $player
 * @property string|null                     $last_coordinate
 * @property string|null                     $until
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null                     $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereLastCoordinate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack wherePlayer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PlayerTrack extends Model
{
    use SoftDeletes;
    //
    protected $guarded = [];
    protected $dates = [
        'until',
        'deleted_at',
    ];
}
