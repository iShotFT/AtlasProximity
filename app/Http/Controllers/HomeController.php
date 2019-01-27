<?php

namespace App\Http\Controllers;

use App\LinkClick;
use App\Ping;
use Barryvdh\Snappy\Facades\SnappyImage;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //        $this->middleware('auth');
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
        $request->validate([
            'src' => 'sometimes|string',
        ]);

        LinkClick::create([
            'ip'        => $request->ip(),
            'useragent' => $request->userAgent(),
            'source'    => $request->get('src'),
        ]);


        return redirect()->to('https://discordapp.com/api/oauth2/authorize?client_id=' . config('atlas.bot.clientid') . '&scope=bot&permissions=' . config('atlas.bot.permission.integer'));
    }
}
