<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\User\CreateController;
use App\Http\Controllers\User\LoginController;


Route::prefix('v1')->group(function () {
    // USER GROUPED ROUTES
    Route::prefix('user')->group(function () {
        Route::post('create', CreateController::class)->name('user.create');
        Route::post('login', LoginController::class)->name('user.login');

        Route::middleware([JwtMiddleware::class])->group(function () {
            // Route::post('/create', [CreateController::class])->name('user.create');
        });
    });
});