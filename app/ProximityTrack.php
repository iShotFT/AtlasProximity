<?php

namespace App;

use App\Traits\HasGuild;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\ProximityTrack
 *
 * @property int                             $id
 * @property string                          $guild_id
 * @property string                          $channel_id
 * @property string                          $coordinate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\ProximityTrack onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereCoordinate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ProximityTrack withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\ProximityTrack withoutTrashed()
 * @mixin \Eloquent
 * @property-read mixed $guild_name
 * @property-read \App\Guild $guild
 * @property string $region
 * @property string $gamemode
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereGamemode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ProximityTrack whereRegion($value)
 */
class ProximityTrack extends Model
{
    protected $with = ['guild'];

    use SoftDeletes, HasGuild;
    //
    protected $dates = ['deleted_at'];
    protected $guarded = [];
}
