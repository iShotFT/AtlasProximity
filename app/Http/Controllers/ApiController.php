<?php

namespace App\Http\Controllers;

use App\PlayerPing;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function population(Request $request)
    {
        $request->validate([
            'server'   => 'required|string|max:3|min:2',
            'region'   => 'required|string|size:2',
            'gamemode' => 'required|string|size:3',
        ]);

        return response()->json(SourceQueryController::getCoordinatePlayersWithSurrounding($request->get('server'), $request->get('region'), $request->get('gamemode')));
    }

    public function find(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:2',
        ]);

        $found = PlayerPing::where('player', 'LIKE', $request->get('username') . '%')->orderByDesc('created_at')->first([
            'player',
            'coordinates',
            'created_at',
        ]);

        return response()->json(($found ? [$found->toArray()] : []));
    }
}
