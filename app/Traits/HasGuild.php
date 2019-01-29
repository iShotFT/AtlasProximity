<?php

namespace App\Traits;

use App\Guild;

trait HasGuild
{
    public function getGuildNameAttribute()
    {
        if ($this->guild()->count()) {
            return $this->guild->name;
        } else {
            return $this->guild_id;
        }
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class, 'guild_id', 'guild_id');
    }
}