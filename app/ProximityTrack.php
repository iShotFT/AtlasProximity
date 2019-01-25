<?php

namespace App;

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
 */
class ProximityTrack extends Model
{
    use SoftDeletes;
    //
    protected $dates = ['deleted_at'];
    protected $guarded = [];
}
