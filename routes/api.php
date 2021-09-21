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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


// Common uses for all promotions
Route::get('get-phone-code', 'CommonController@phoneCode');
Route::get('client-timezone-data', 'CommonController@getClientTimeZoneData');
Route::get('get-client-ip', 'CommonController@getIp');
Route::get('full-list', 'CommonController@fullListOfWebinar');
Route::get('webhook', 'CommonController@webhookCall');
Route::get('start-in', 'CommonController@startsIn');
Route::get('currency/{currencyCode}', 'CommonController@getCurrencyInfo');
Route::get('currencies', 'CommonController@currencies');

// integration
Route::get('sms', 'IntrgrationController@getSmsReadySchdule');

Route::get('test', 'GcController@test');

// gc
Route::get('gc/get-schedule/{timezone}', 'WorkshopController@getDetails');
Route::put('gc/set-schedule/{timezone}', 'WorkshopController@setSchedule');
Route::get('free/get-thankyou-data/{id}', 'WorkshopController@getThankyou');

Route::get('gc/download', 'GcController@download');
Route::get('gc/download/image', 'GcController@download2');


