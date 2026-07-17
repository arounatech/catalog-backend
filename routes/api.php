<?php

use App\Http\Controllers\Api\Admin\PortfolioController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\Admin\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API is working',
        'status' => 'success',
    ]);
});

Route::prefix('admin')->group(function () {
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('settings', SettingController::class);
    Route::apiResource('portfolios', PortfolioController::class);
});