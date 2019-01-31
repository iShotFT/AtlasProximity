<?php

namespace App\Http\Controllers;

use App\Events\BotUpdated;
use App\Update;
use Illuminate\Http\Request;

class UpdateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $updates = Update::orderByDesc('created_at')->limit(1000)->get();

        return view('update.index', compact('updates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $previous_update = Update::orderByDesc('created_at')->first();

        return view('update.create', compact('previous_update'));
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
            'version' => 'required',
            'major'   => 'required',
            'minor'   => 'required',
            'changes' => 'required',
        ]);

        $update = Update::create($request->all());

        // Notify the bot about the update
        event(new BotUpdated($update));

        return redirect()->route('update.index');
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
     * @throws \Exception
     */
    public function destroy(Request $request)
    {
//        dd('hi');
        $update = Update::findOrFail($request->get('id'));
        $update->delete();

        return redirect()->route('update.index');
    }
}
