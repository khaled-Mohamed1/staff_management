<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\EmployeeController;
use App\Http\Controllers\api\SettingController;
use App\Http\Controllers\api\ConversationController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login',[AuthController::class, 'login']);
Route::post('webhook', [MessageController::class, 'webhook']);
Route::get('pusher', [AuthController::class, 'pusher']);

Route::group([

    'middleware' => ['auth:sanctum'],
    'prefix' => 'auth'

], function ($router) {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('employee')->group(function () {

        Route::get('index', [EmployeeController::class, 'index']);
        Route::post('store', [EmployeeController::class, 'store']);
        Route::post('update', [EmployeeController::class, 'update']);
        Route::post('show', [EmployeeController::class, 'show']);
        Route::post('delete', [EmployeeController::class, 'delete']);

    });

    Route::prefix('setting')->group(function () {

        Route::post('update', [SettingController::class, 'update']);
        Route::post('show', [SettingController::class, 'show']);

    });

    Route::prefix('conversations')->group(function () {

        Route::post('chats', [ConversationController::class, 'index']);
        Route::post('show', [ConversationController::class, 'show']);
        Route::post('update', [ConversationController::class, 'update']);
        Route::post('read', [ConversationController::class, 'read']);

    });

    Route::prefix('messages')->group(function () {

        Route::post('send_message', [MessageController::class, 'sendMessage']);
        Route::post('send_image', [MessageController::class, 'sendImage']);
        Route::post('send_voice', [MessageController::class, 'sendVoice']);
        Route::post('send_document', [MessageController::class, 'sendDocument']);

    });
});
