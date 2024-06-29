<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\User\CreateController;


Route::prefix('v1')->group(function () {
    // USER GROUPED ROUTES
    Route::prefix('user')->group(function () {
        // Route::post('/create', [CreateController::class])->name('user.create');
        Route::post('/create', function (Request $request) {
            return "Gotcha!!!";
        });

        Route::middleware([JwtMiddleware::class])->group(function () {
            // Route::post('/create', [CreateController::class])->name('user.create');
        });
    });
});