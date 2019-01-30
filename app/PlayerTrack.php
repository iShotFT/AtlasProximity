<?php

namespace App;

use App\Traits\HasGuild;
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
 * @property string|null                     $last_direction
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\PlayerTrack onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereLastDirection($value)
 * @method static \Illuminate\Database\Query\Builder|\App\PlayerTrack withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\PlayerTrack withoutTrashed()
 * @property int                             $last_status
 * @method static \Illuminate\Database\Eloquent\Builder|\App\PlayerTrack whereLastStatus($value)
 * @property-read mixed $guild_name
 * @property-read \App\Guild $guild
 */
class PlayerTrack extends Model
{
    use SoftDeletes, HasGuild;
    protected $with = ['guild'];
    //
    protected $guarded = [];
    protected $dates = [
        'until',
        'deleted_at',
    ];
}
