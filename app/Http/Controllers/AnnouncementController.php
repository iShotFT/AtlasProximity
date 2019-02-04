<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\Command;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function callback(Announcement $announcement)
    {
        // Catch the callback from the bot, it should post to this route after doing the announcement with info.
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = Announcement::orderByDesc('created_at')->limit(1000)->get();

        return view('announcement.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('announcement.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'   => 'required',
            'message' => 'required',
        ]);

        if ($request->has('channels') && !empty($request->get('channels'))) {
            // Specific channels
            $channels = ['channels' => explode(', ', $request->get('channels'))];
        } else {
            // All channels
            $channels = ['channels' => Command::all()->unique('channel_id')->pluck('channel_id')];
        }

        $announcement = Announcement::create(array_merge($request->all(), $channels));

        // Notify the bot about the update
        event(new \App\Events\Announcement($announcement));

        return redirect()->route('announcement.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
