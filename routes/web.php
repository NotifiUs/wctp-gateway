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
    Route::post('/account/name', 'UpdateName');
    Route::post('/account/timezone', 'UpdateTimezone');
    Route::post('/account/email', 'ChangeEmail');
    Route::post('/account/password', 'ChangePassword');
});

Route::namespace('Events')->group(function () {
    Route::get('/events', 'ShowEvents');
});

Route::namespace('System')->group(function () {
    Route::get('/system/information', 'ShowSysInfo');
    Route::get('/system/phpinfo', 'ShowPHPInfo');
    Route::get('/system', 'ShowSystem');
    Route::post('/system/maintenance/disable', 'DisableMaintenanceMode');
    Route::post('/system/maintenance/enable', 'EnableMaintenanceMode');
    Route::post('/system/user/create', 'CreateUser');
    Route::post('/system/user/edit/{user}', 'EditUser');
    Route::post('/system/user/delete/{user}', 'DeleteUser');
});

Route::namespace('Carriers')->group(function () {
    Route::get('/carriers', 'ShowCarriers');
    Route::post('/carriers', 'CreateCarrier');
    Route::post('/carriers/{carrier}/edit', 'EditCarrier');
    Route::post('/carriers/{carrier}/enable', 'EnableCarrier');
    Route::post('/carriers/{carrier}/disable', 'DisableCarrier');
    Route::post('/carriers/{carrier}/delete', 'DeleteCarrier');
    Route::post('/carriers/verify', 'VerifyCarrier');
});

Route::namespace('SMS')->group(function(){
   Route::post('/sms/inbound/{identifier}/primary', 'PrimaryHandler' );
   Route::post('/sms/inbound/{identifier}/fallback', 'FallbackHandler' );
   Route::post('/sms/callback/{identifier}/status', 'StatusHandler' );
});

Route::namespace('Numbers')->group(function(){
   Route::get('/numbers', 'ShowNumbers');
   Route::post('/numbers', 'CreateNumber');
    Route::post('/numbers/{number}/enable', 'EnableNumber');
    Route::post('/numbers/{number}/disable', 'DisableNumber');
    Route::post('/numbers/{number}/delete', 'DeleteNumber');
    Route::post('/numbers/{number}/setup', 'SetupNumber');
});

Route::namespace('EnterpriseHosts')->group(function(){
    Route::get('/hosts', 'ShowHosts');
    Route::post('/hosts', 'CreateHost');
    Route::post('/hosts/{host}/edit', 'EditHost');
    Route::post('/hosts/{host}/enable', 'EnableHost');
    Route::post('/hosts/{host}/disable', 'DisableHost');
    Route::post('/hosts/{host}/delete', 'DeleteHost');
});

Route::namespace('WCTP')->group(function(){
    Route::post('/wctp', 'Inbound');
});

Route::namespace('Analytics')->group(function(){
    Route::get('/analytics', 'ShowAnalytics');
});


Route::get('/sticky', 'UnderConstruction');
