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

Route::get('population', 'ApiController@population');
Route::get('players', 'ApiController@players');
Route::get('find', 'ApiController@find');
Route::get('track/list', 'ApiController@trackList');

Route::post('track/add', 'ApiController@trackAdd');

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
