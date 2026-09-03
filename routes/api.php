<?php

use App\Http\Controllers\Api\ChildrenController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Google Login
    Route::post('auth/google-login', [GoogleAuthController::class, 'login']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {

        // Logout
        Route::post('auth/logout', [GoogleAuthController::class, 'logout']);

        // Profile
        Route::post('profile/update', [ProfileController::class, 'update']);

        // Children
        Route::post('children', [ChildrenController::class, 'store']);
        Route::get('children', [ChildrenController::class, 'index']);
    });
});