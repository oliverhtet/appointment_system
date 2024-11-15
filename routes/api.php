<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceProviderController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


Route::prefix('service-providers')->middleware('auth:api')->group(function () {
    Route::get('/', [ServiceProviderController::class, 'index']);
    Route::post('/', [ServiceProviderController::class, 'store']);
    Route::get('{id}', [ServiceProviderController::class, 'show']);
    Route::put('{id}', [ServiceProviderController::class, 'update']);
    Route::delete('{id}', [ServiceProviderController::class, 'destroy']);
});

Route::prefix('services')->group(function () {
    Route::get('/', [ServiceController::class, 'index']);
    Route::post('/', [ServiceController::class, 'store']);
    Route::get('{id}', [ServiceController::class, 'show']);
    Route::put('{id}', [ServiceController::class, 'update']);
    Route::delete('{id}', [ServiceController::class, 'destroy']);
});

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']); 
    Route::post('/', [BookingController::class, 'store']); 
    Route::get('/{id}', [BookingController::class, 'show']); 
    Route::put('/{id}', [BookingController::class, 'update']); 
    Route::delete('/{id}', [BookingController::class, 'destroy']); 
});

 Route::prefix('payments')->group(function () {
    Route::get('/', [PaymentController::class, 'index']); 
    Route::post('/pay/{bookingId}', [PaymentController::class, 'pay']); 
    Route::post('/confirm/{paymentId}', [PaymentController::class, 'confirm']); 
});
