<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Announcement
 *
 * @property int                             $id
 * @property string                          $title
 * @property string                          $message
 * @property string                          $channels
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereChannels($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Announcement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Announcement extends Model
{
    protected $guarded = [];

    protected $casts = [
        'channels' => 'array',
    ];

}
