<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Guild
 *
 * @property int $id
 * @property string $name
 * @property string $guild_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Guild onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Guild whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Guild withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Guild withoutTrashed()
 * @mixin \Eloquent
 */
class Guild extends Model
{
    use SoftDeletes;
    //
    protected $guarded = [];
    protected $dates = ['deleted_at'];
}
