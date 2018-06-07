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
    Route::post('check', 'TokenController@check');
    Route::post('generate', 'TokenController@generate');
});

Route::prefix('task')->middleware('CheckToken')->group(function(){
    Route::post(    '/',            'TaskController@create');
    Route::get(     '/',            'TaskController@retrive');
    Route::put(     '/{task}',      'TaskController@update');
    Route::delete(  '/{task}',      'TaskController@delete');
});