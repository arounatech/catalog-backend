<?php

use App\Http\Controllers\Api\Admin\PortfolioController;
use App\Http\Controllers\Api\Admin\ProjectController;
use App\Http\Controllers\Api\Admin\ProjectImageController;
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
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('project-images', ProjectImageController::class);
});