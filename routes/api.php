<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('token')->group(function(){
    Route::post     ('check',           'TokenController@check');
    Route::post     ('generate',        'TokenController@generate');
});

Route::prefix('task')->middleware('CheckToken')->group(function(){
    Route::post     ('/',               'TaskController@create');
    Route::get      ('/',               'TaskController@retrive');
    Route::put      ('/{task}',         'TaskController@update');
    Route::delete   ('/{task}',         'TaskController@delete');
});

Route::prefix('tag')->middleware('CheckToken')->group(function(){
    Route::post     ('/{task}',         'TagController@create');
});

Route::prefix('track')->middleware('CheckToken')->group(function(){
    Route::post     ('/start/{task}',   'TrackController@start');
    Route::post     ('/finish/{track}', 'TrackController@finish');
    Route::post     ('/insert/{task}',  'TrackController@insert');
    Route::put      ('/update/{track}', 'TrackController@update');
    Route::delete   ('/delete/{track}', 'TrackController@delete');
});