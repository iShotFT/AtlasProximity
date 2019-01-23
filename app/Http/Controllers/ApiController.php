<?php

namespace App\Http\Controllers;

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
}
