<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/test', 'SourceQueryController@test')->name('test');
Route::get('/get', 'HomeController@get')->name('get');

//Route::get('/help', 'HomeController@help')->name('help');
//Route::get('/map', 'HomeController@map')->name('map');
//Route::get('/bot', 'DiscordBotController@run')->name('bot');
//Route::get('/poll', 'SourceQueryController@serverGetPlayers')->name('poll');
//Route::get('/surround', 'SourceQueryController@getSurroundingServers')->name('surround');
Route::group(['middleware' => ['permission:a.update']], function () {
    Route::resource('update', 'UpdateController');
    Route::get('faq/destroy', 'UpdateController@destroy')->name('update.get.destroy');
});

Route::group(['middleware' => ['permission:a.faq']], function () {
    Route::resource('faq', 'FaqController');
    Route::get('faq/destroy', 'FaqController@destroy')->name('faq.get.destroy');
});