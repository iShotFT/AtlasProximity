<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property-read mixed                      $full_version
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Update onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Update whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Update withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Update withoutTrashed()
 */
class Update extends Model
{
    use SoftDeletes;
    //
    protected $guarded = [];
    protected $dates = ['deleted_at'];

    public function getFullVersionAttribute()
    {
        return ($this->version ? $this->version : 'B') . '.' . $this->major . '.' . $this->minor;
    }
}