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

Route::get('help', 'ApiController@help');
Route::get('faq', 'ApiController@faq');
Route::get('map', 'ApiController@map');
Route::get('version', 'ApiController@version');
Route::get('population', 'ApiController@population');
Route::get('players', 'ApiController@players');
Route::get('find', 'ApiController@find');
Route::get('track/list', 'ApiController@trackList');
Route::get('proximity/list', 'ApiController@proximityList');

Route::post('guild/add', 'ApiController@guildAdd');
Route::post('track/add', 'ApiController@trackAdd');
Route::post('proximity/add', 'ApiController@proximityAdd');

Route::post('guild/remove', 'ApiController@guildRemove');
Route::post('track/remove', 'ApiController@trackRemove');
Route::post('proximity/remove', 'ApiController@proximityRemove');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
