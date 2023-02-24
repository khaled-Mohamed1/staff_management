<?php

use App\Http\Controllers\api\MessageController;
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

Route::get('/', function () {
    return view('welcome');
});

//ngrok
Route::get('/send-message', [MessageController::class, 'sendMessageTwo']);
Route::get('/get_url', [MessageController::class, 'getUrl']);
Route::get('/download_media', [MessageController::class, 'downloadMedia']);
Route::get('/whatsapp-webhook', [MessageController::class, 'verifyWebhook']);
Route::post('/whatsapp-webhook', [MessageController::class, 'processWebhook']);

