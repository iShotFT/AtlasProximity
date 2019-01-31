<?php

namespace App\Http\Controllers;

use App\LinkClick;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function get(Request $request)
    {
        LinkClick::create([
            'ip'        => $request->getClientIp(),
            'useragent' => $request->userAgent(),
            'source'    => $request->get('src'),
        ]);

        return redirect()->to('https://discordapp.com/api/oauth2/authorize?client_id=' . config('atlas.bot.clientid') . '&scope=bot&permissions=' . config('atlas.bot.permission.integer'));
    }
}
