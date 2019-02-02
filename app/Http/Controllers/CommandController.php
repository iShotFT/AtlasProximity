<?php

namespace App\Http\Controllers;

use App\Command;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function index(Request $request)
    {
        $commands = Command::orderByDesc('created_at')->limit(1000)->get();

        return view('command.index', compact('commands'));
    }
}
