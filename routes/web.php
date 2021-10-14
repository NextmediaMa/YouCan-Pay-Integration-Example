<?php

use App\Http\Controllers\PayController;
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

Route::prefix('integrations')->name('integrations.')->group(function () {
    Route::get('widget', [PayController::class, 'widget'])
        ->name('widget');
    Route::get('standalone', [PayController::class, 'standalone'])
        ->name('standalone');
});

Route::get('callback', [PayController::class, 'callback'])
    ->name('callback');

Route::post('verify', [PayController::class, 'verify'])
    ->name('verify');
