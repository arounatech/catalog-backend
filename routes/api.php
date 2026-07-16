<?php

use App\Http\Controllers\Api\Admin\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'API is working',
        'status' => 'success',
    ]);
});

Route::prefix('admin')->group(function () {
    Route::apiResource('services', ServiceController::class);
});