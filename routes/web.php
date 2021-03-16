<?php

use Illuminate\Support\Facades\Route;

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
Route::get('login/{driver}', 'API\AuthController@redirectToProvider')->name('auth.social');
Route::get('login/{driver}/callback', 'API\AuthController@handleProviderCallback')->name('auth.social.callback');
Route::get('/', function () {
    return view('welcome');
});
