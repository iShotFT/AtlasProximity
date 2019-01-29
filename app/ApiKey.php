<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

/**
 * App\ApiKey
 *
 * @property-read \App\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\ApiKey onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\ApiKey withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\ApiKey withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\ApiKey whereUserId($value)
 */
class ApiKey extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $dates = ['deleted_at'];

    // Autogenerate a key when a model is made
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->key = (string)Uuid::generate(4);
        });
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
