<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('settings', 'ApiController@settings')->name('api.settings');
Route::get('help', 'ApiController@help')->name('api.help');
Route::post('guild/add', 'ApiController@guildAdd')->name('api.guild.add');
Route::post('guilds/add', 'ApiController@guildsAdd')->name('api.guilds.add');
Route::post('announcement/callback', 'AnnouncementController@callback')->name('api.announcement.callback');

Route::get('faq', 'ApiController@faq');
Route::get('map', 'ApiController@map');
Route::get('version', 'ApiController@version');
Route::get('population', 'ApiController@population');
Route::get('players', 'ApiController@players');
Route::get('find', 'ApiController@find');
Route::get('stats', 'ApiController@stats');
Route::get('findboat', 'ApiController@findBoat');
Route::get('track/list', 'ApiController@trackList');
Route::get('proximity/list', 'ApiController@proximityList');

Route::post('command/add', 'ApiController@commandAdd');
Route::post('track/add', 'ApiController@trackAdd');
Route::post('proximity/add', 'ApiController@proximityAdd');

Route::post('guild/remove', 'ApiController@guildRemove');
Route::post('track/remove', 'ApiController@trackRemove');
Route::post('proximity/remove', 'ApiController@proximityRemove');
Route::post('track/remove/all', 'ApiController@trackRemoveAll');
Route::post('proximity/remove/all', 'ApiController@proximityRemoveAll');
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
