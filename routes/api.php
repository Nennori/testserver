<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::delete('logout', 'API\AuthController@logout');
Route::post('register', 'API\AuthController@register');
Route::post('login', 'API\AuthController@login');
Route::post('refresh', 'API\AuthController@refresh');


Route::middleware(['auth:api', 'check'])->group(function(){
    Route::get('user', 'API\AuthController@getUser');
    Route::get('boards', 'API\BoardController@index');
    Route::get('boards/{board}/status', 'API\BoardController@getStatuses');
    Route::post('boards', 'API\BoardController@store');
    Route::post('boards/{board}/mark', 'API\BoardController@addMark');
    Route::post('boards/{board}/user', 'API\BoardController@addUser');
    Route::post('boards/{board}/status', 'API\BoardController@addStatus');
    Route::put('boards/{board}', 'API\BoardController@update');
    Route::put('user', 'API\AuthController@editUser');
    Route::delete('boards/{board}/status', 'API\BoardController@deleteStatus');
    Route::delete('boards/{board}', 'API\BoardController@destroy');
    Route::delete('boards/{board}/user', 'API\BoardController@deleteUser');
    Route::delete('boards/{board}/mark', 'API\BoardController@deleteMark');
});

Route::middleware(['auth:api', 'check'])->group(function(){
    Route::get('boards/{board}/tasks', 'API\TaskController@index');
    Route::get('boards/{board}/tasks/{task}', 'API\TaskController@show');
    Route::post('boards/{board}/tasks', 'API\TaskController@store');
    Route::post('boards/{board}/tasks/{task}/user', 'API\TaskController@addUser');
    Route::post('boards/{board}/tasks/{task}/mark', 'API\TaskController@addMark');
    Route::put('boards/{board}/tasks/{task}/status', 'API\TaskController@changeStatus');
    Route::put('boards/{board}/tasks/{task}', 'API\TaskController@update');
    Route::delete('boards/{board}/tasks/{task}', 'API\TaskController@destroy');
    Route::delete('boards/{board}/tasks/{task}/user', 'API\TaskController@deleteUser');
    Route::delete('boards/{board}/tasks/{task}/mark', 'API\TaskController@deleteMark');

});
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
