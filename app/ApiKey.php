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
