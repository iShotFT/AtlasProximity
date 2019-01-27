<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LinkClick
 *
 * @property int $id
 * @property string|null $ip
 * @property string|null $useragent
 * @property string|null $source
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkClick whereUseragent($value)
 * @mixin \Eloquent
 */
class LinkClick extends Model
{
    //
    protected $guarded = [];
}
