<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\User\CreateController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\ViewController;
use App\Http\Controllers\User\EditController;
use App\Http\Controllers\User\LogoutController;
use App\Http\Controllers\User\DeleteController;

Route::prefix('v1')->group(function () {
    // USER GROUPED ROUTES
    Route::prefix('user')->group(function () {
        Route::post('create', CreateController::class)->name('user.create');
        Route::post('login', LoginController::class)->name('user.login');

        Route::middleware([JwtMiddleware::class])->group(function () {
            Route::get('/', ViewController::class)->name('user.view');
            Route::put('/edit', EditController::class)->name('user.edit');
            Route::get('/logout', LogoutController::class)->name('user.logout');
            Route::delete('/', DeleteController::class)->name('user.delete');
        });
    });
});
