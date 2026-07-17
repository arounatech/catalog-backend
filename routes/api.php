<?php

use App\Http\Controllers\Api\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Api\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Api\Admin\ProjectImageController as AdminProjectImageController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Api\Frontend\PortfolioController as FrontendPortfolioController;
use App\Http\Controllers\Api\Frontend\ProjectController as FrontendProjectController;
use App\Http\Controllers\Api\Frontend\ServiceController as FrontendServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API is working',
        'status' => 'success',
    ]);
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::apiResource('services', AdminServiceController::class);
    Route::apiResource('settings', AdminSettingController::class);
    Route::apiResource('portfolios', AdminPortfolioController::class);
    Route::apiResource('projects', AdminProjectController::class);
    Route::apiResource('project-images', AdminProjectImageController::class);
});

Route::prefix('public')->name('public.')->group(function () {
    Route::apiResource('services', FrontendServiceController::class)->only(['index', 'show']);
    Route::apiResource('portfolios', FrontendPortfolioController::class)->only(['index', 'show']);
    Route::apiResource('projects', FrontendProjectController::class)->only(['index', 'show']);
});