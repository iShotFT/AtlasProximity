<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Faq
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Faq onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Faq whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Faq withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Faq withoutTrashed()
 * @mixin \Eloquent
 */
class Faq extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $guarded = [];
    //
}
