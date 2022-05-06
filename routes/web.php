<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Api;

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
    return redirect()->route('admin.home');
});

Route::post('/telegram/webhook', function (){
    \Telegram\Bot\Laravel\Facades\Telegram::commandsHandler(true);
    (new \App\Http\Handler\Telegram\ButtonHandler())->handle(app()->make(Api::class)->getWebhookUpdates());
});