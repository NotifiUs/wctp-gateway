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

// No registration allowed, user must verify email.
Auth::routes(['register' => false, 'verify' => true ]);

//
Route::get('/', 'RedirectToDashboard');

Route::get('/home', 'ShowDashboard')->name('home');

Route::namespace('Account')->group(function () {
    Route::get('/account', 'ShowAccount');
    Route::get('/account/verify-email', 'VerifyEmail')->middleware('verified');
});

Route::namespace('Carriers')->group(function () {
    Route::get('/carriers', 'ShowCarriers');
});

Route::namespace('EnterpriseHosts')->group(function(){
    Route::get('/hosts', 'ShowHosts');
});

Route::namespace('WCTP')->group(function(){
    Route::post('/wctp', 'Inbound');
});



Route::get('/analytics', 'UnderConstruction');
Route::get('/events', 'UnderConstruction');
Route::get('/sticky', 'UnderConstruction');
Route::get('/system', 'UnderConstruction');

