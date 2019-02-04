<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Monitor
 *
 * @property int $id
 * @property string $guild_id
 * @property string $channel_id
 * @property string $coordinate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Monitor onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor whereCoordinate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Monitor whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Monitor withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Monitor withoutTrashed()
 * @mixin \Eloquent
 */
class Monitor extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = [];
}
