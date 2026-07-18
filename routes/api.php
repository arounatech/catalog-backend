<?php

use App\Http\Controllers\Api\Admin\Auth\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Api\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Api\Admin\ProjectImageController as AdminProjectImageController;
use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\Admin\ServiceRequestController as AdminServiceRequestController;
use App\Http\Controllers\Api\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Api\Frontend\PortfolioController as FrontendPortfolioController;
use App\Http\Controllers\Api\Frontend\ProjectController as FrontendProjectController;
use App\Http\Controllers\Api\Frontend\ServiceController as FrontendServiceController;
use App\Http\Controllers\Api\Frontend\ServiceRequestController as FrontendServiceRequestController;
use App\Http\Controllers\Api\User\Auth\AuthController as UserAuthController;
use App\Http\Controllers\Api\User\ServiceRequestController as UserServiceRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API is working',
        'status' => 'success',
    ]);
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('auth/login', [AdminAuthController::class, 'login'])
        ->name('auth.login');

    Route::middleware(['auth:sanctum', 'ensure.admin'])->group(function () {
        Route::get('auth/me', [AdminAuthController::class, 'me'])
            ->name('auth.me');

        Route::post('auth/logout', [AdminAuthController::class, 'logout'])
            ->name('auth.logout');

        Route::apiResource('services', AdminServiceController::class);
        Route::apiResource('settings', AdminSettingController::class);
        Route::apiResource('portfolios', AdminPortfolioController::class);
        Route::apiResource('projects', AdminProjectController::class);
        Route::apiResource('project-images', AdminProjectImageController::class);

        Route::apiResource('service-requests', AdminServiceRequestController::class)
            ->only(['index', 'show', 'update', 'destroy']);
    });
});

Route::prefix('user')->name('user.')->group(function () {
    Route::post('auth/register', [UserAuthController::class, 'register'])
        ->name('auth.register');

    Route::post('auth/login', [UserAuthController::class, 'login'])
        ->name('auth.login');

    Route::middleware(['auth:sanctum', 'ensure.user'])->group(function () {
        Route::get('auth/me', [UserAuthController::class, 'me'])
            ->name('auth.me');

        Route::post('auth/logout', [UserAuthController::class, 'logout'])
            ->name('auth.logout');

        Route::apiResource('service-requests', UserServiceRequestController::class)
            ->only(['index', 'store']);
    });
});

Route::prefix('public')->name('public.')->group(function () {
    Route::apiResource('services', FrontendServiceController::class)
        ->only(['index', 'show']);

    Route::apiResource('portfolios', FrontendPortfolioController::class)
        ->only(['index', 'show']);

    Route::apiResource('projects', FrontendProjectController::class)
        ->only(['index', 'show']);

    Route::post('service-requests', [FrontendServiceRequestController::class, 'store'])
        ->name('service-requests.store');
});