<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

// PIX Payment routes
Route::prefix('pix-payments')->middleware('auth:sanctum')->group(function () {
    Route::get('/statuses', [\App\Http\Controllers\PixPaymentController::class, 'statuses']);
    Route::get('/', [\App\Http\Controllers\PixPaymentController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\PixPaymentController::class, 'store']);
    Route::get('/{id}', [\App\Http\Controllers\PixPaymentController::class, 'show']);
    Route::post('/{id}/check-status', [\App\Http\Controllers\PixPaymentController::class, 'checkStatus']);
});

// Withdrawal routes
Route::prefix('withdrawals')->middleware('auth:sanctum')->group(function () {
    Route::get('/statuses', [\App\Http\Controllers\WithdrawalController::class, 'statuses']);
    Route::get('/', [\App\Http\Controllers\WithdrawalController::class, 'index']);
    Route::post('/', [\App\Http\Controllers\WithdrawalController::class, 'store']);
    Route::get('/{id}', [\App\Http\Controllers\WithdrawalController::class, 'show']);
    Route::post('/{id}/check-status', [\App\Http\Controllers\WithdrawalController::class, 'checkStatus']);
});


