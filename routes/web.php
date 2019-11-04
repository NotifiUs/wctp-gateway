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

Auth::routes(['register' => false, 'verify' => true ]);

Route::get('/', 'HomeController@redirect');

Route::get('/home', 'HomeController@index')->name('home');

Route::namespace('Account')->group(function () {
    Route::get('/account', 'ShowAccount');
    Route::get('/account/verify-email', 'VerifyEmail')->middleware('verified');
});


Route::get('/analytics', 'UnderConstructionController@index');
Route::get('/carriers', 'UnderConstructionController@index');
Route::get('/hosts', 'UnderConstructionController@index');
Route::get('/events', 'UnderConstructionController@index');
Route::get('/sticky', 'UnderConstructionController@index');
Route::get('/system', 'UnderConstructionController@index');
