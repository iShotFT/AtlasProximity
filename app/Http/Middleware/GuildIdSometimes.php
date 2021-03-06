<?php

namespace App\Http\Middleware;

use App\Guild;
use Closure;

class GuildIdSometimes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('guildid')) {
            if ($guild = Guild::where('guild_id', $request->get('guildid'))->first()) {
                // Check if the settings for this guild are correctly set
                if (is_null($guild->region) || is_null($guild->gamemode)) {
                    if (config('app.url') . $request->getPathInfo() === route('api.help')) {
                        // This route is allowed without guildid
                        return $next($request);
                    }

                    if (config('app.url') . $request->getPathInfo() === route('api.settings')) {
                        // This route is allowed without guildid
                        return $next($request);
                    }

                    // Server is missing one or more settings
                    return response()->json(['message' => ':warning: This Discord server is missing required configurations. Use `!settings` for more info (needs server administrative permissions). Also read the installation instruction that can be found here: https://atlasdiscordbot.com/docs/beta/installation'], 400);
                }

                // Add information to the request so we can catch it later on
                $request->request->add(['region' => $guild->region]);
                $request->request->add(['gamemode' => $guild->gamemode]);

                return $next($request);
            } else {
                if (config('app.url') . $request->getPathInfo() === route('api.guild.add')) {
                    // This route is allowed without existing discord server in db
                    return $next($request);
                }

                // Guild with this ID doesn't exist in our database
                return response()->json(['message' => ':warning: This Discord server is missing required configurations. Use `!settings` for more info (needs server administrative permissions). Also read the installation instruction that can be found here: https://atlasdiscordbot.com/docs/beta/installation'], 400);
            }
        } else {
            if (config('app.url') . $request->getPathInfo() === route('api.announcement.callback')) {
                // This route is allowed without guildid
                return $next($request);
            }

            if ($request->has('data')) {
                return $next($request);
            }

            return response()->json(['message' => 'Your request is missing a Discord guild_id parameter'], 400);
        }
    }
}
