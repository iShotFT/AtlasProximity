<?php

namespace App;

use App\Traits\HasGuild;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Command
 *
 * @property int                             $id
 * @property string                          $user
 * @property string                          $command
 * @property string                          $arguments
 * @property string                          $guild_id
 * @property string                          $channel_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command whereArguments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command whereGuildId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Command whereUser($value)
 * @mixin \Eloquent
 */
class Command extends Model
{
    protected $with = ['guild'];

    use HasGuild;
    //
    protected $guarded = [];
}
