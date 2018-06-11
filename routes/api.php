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

Route::prefix('task')->middleware(['CheckToken', 'cors'])->group(function(){
    Route::post     ('/',               'TaskController@create');
    
    Route::post      ('/',               'TaskController@retrive');
    Route::post      ('/count',          'TaskController@count');
    
    Route::put      ('/{task}',         'TaskController@update');
    Route::delete   ('/{task}',         'TaskController@delete');

    Route::post     ('/finish/{task}',  'TaskController@finish');

    Route::post      ('/sum',            'TaskController@sum');
});

Route::prefix('tag')->middleware(['CheckToken', 'cors'])->group(function(){
    Route::post     ('/{task}',         'TagController@create');
    Route::post      ('/',               'TagController@retrive');
});

Route::prefix('track')->middleware(['CheckToken', 'cors'])->group(function(){
    Route::post     ('/start/{task}',   'TrackController@start');
    Route::post     ('/finish/{track}', 'TrackController@finish');
    Route::post     ('/insert/{task}',  'TrackController@insert');
    Route::put      ('/{track}',        'TrackController@update');
    Route::delete   ('/{track}',        'TrackController@delete');

    Route::post      ('/',               'TrackController@retrive');
    Route::post      ('/count',          'TrackController@count');

    Route::post      ('/sum',            'TrackController@sum');
});