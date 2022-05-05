<?php

use App\Http\Controllers\Api\DiscountController;
use App\Http\Controllers\Api\UserDiscountController;
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
Route::middleware(PhoneNumberAuth::class)->group(function () {
    Route::apiResource('discounts', DiscountController::class)
    ->only([
      'index',
      'store',
    ]);

    Route::apiResource('discounts.users', UserDiscountController::class)
    ->only([
      'index',
    ]);
});
