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

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('/logout', 'App\Http\Controllers\Auth\AuthController@logout');
    Route::get('/user', 'App\Http\Controllers\Auth\AuthController@fetchAuthUser');
    Route::get('/users', 'App\Http\Controllers\Auth\AuthController@fetchAllUsers');
    Route::get('/contacted-users', 'App\Http\Controllers\Auth\AuthController@fetchContactedUsers');

    // Info Updates
    Route::post('/update-info', 'App\Http\Controllers\Auth\AuthController@updateUserInfo');

    // Avatar Update
    Route::post('/update-dp', 'App\Http\Controllers\Auth\AuthController@updateUserDp');

    // Messaging Endpoints
    Route::post('/send-message', 'App\Http\Controllers\Whatsapp\WossopMessageController@sendMessage');
    Route::get('/message/{id}', 'App\Http\Controllers\Whatsapp\WossopMessageController@fetchUserMessages');
    Route::get('/receive-message-from-user/{id}', 'App\Http\Controllers\Whatsapp\WossopMessageController@fetchUserMessages');
    Route::put('/receive-message/{id}', 'App\Http\Controllers\Whatsapp\WossopMessageController@fetchUserMessages');
    // Get File
    Route::get('/message/file/{id}', 'App\Http\Controllers\Whatsapp\WossopMessageController@fetchFile');

    // Get list user history mess
    Route::get('/message/listuser/history', 'App\Http\Controllers\Whatsapp\WossopMessageController@fetchListUserMessages');
    Route::get('/get-user-details/{id}', 'App\Http\Controllers\Auth\AuthController@detail');
    Route::post('/update-socket-id/{user_id}/{socket_id}', 'App\Http\Controllers\Auth\AuthController@updateSocketId');

   // Messaging Endpoints
    Route::post('/send-message', 'App\Http\Controllers\Whatsapp\WossopMessageController@sendMessage');
    Route::get('/message/{id}/{is_group?}', 'App\Http\Controllers\Whatsapp\WossopMessageController@fetchUserMessages');

    //users list
    Route::get('/list-users/{logged_id}', [App\Http\Controllers\UsersController::class, 'index']);

    //Add group
    Route::post('/group','App\Http\Controllers\GroupController@store');
});
Route::post('/register', 'App\Http\Controllers\Auth\AuthController@register');
Route::post('/login', 'App\Http\Controllers\Auth\AuthController@login');

// Broadcast routes for API
Broadcast::routes(['middleware' => ['auth:sanctum']]);


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
    Route::post('/refresh', [App\Http\Controllers\AuthController::class, 'refresh']);
    Route::get('/user-profile', [App\Http\Controllers\AuthController::class, 'userProfile']);
    Route::post('/change-pass', [App\Http\Controllers\AuthController::class, 'changePassWord']);
});
