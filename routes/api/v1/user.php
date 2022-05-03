<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\PhoneNumberAuth;
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
Route::apiResource('users', UserController::class)
  ->only([
      'store',
  ]);

Route::middleware(PhoneNumberAuth::class)->group(function () {
    Route::apiResource('users', UserController::class)
    ->only([
      'show',
    ]);
});
