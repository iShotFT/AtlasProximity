<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Update
 *
 * @property int                             $id
 * @property int                             $version
 * @property int                             $major
 * @property int                             $minor
 * @property string|null                     $changes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update whereChanges($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update whereMajor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update whereMinor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update whereVersion($value)
 * @mixin \Eloquent
 */
class Update extends Model
{
    //
    protected $guarded = [];

    public function getFullVersionAttribute()
    {
        return (($this->version ? $this->version : 'B') . '.' . $this->major . '.' . $this->minor;
    }
}
