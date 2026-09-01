<?php

use App\Http\Controllers\Api\ChildrenController;
use App\Http\Controllers\Api\GoogleAuthController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('auth/google-login', [GoogleAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        Route::post('profile/update', [ProfileController::class, 'update']);

        Route::post('children', [ChildrenController::class, 'store']);
        Route::get('children', [ChildrenController::class, 'index']);
    });
});