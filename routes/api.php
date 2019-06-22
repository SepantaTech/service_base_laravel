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

Route::get('ping', function(){
    return response('OK', 200);
});

// todo | uncomment
// Route::get('register_app', 'RegistrationController@registerApp');

\Illuminate\Support\Facades\Route::middleware(\App\Http\Middleware\AuthByAPIKey::class)->post('test', function(){
    return 'hello';
});
