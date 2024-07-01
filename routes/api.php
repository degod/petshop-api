<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JwtMiddleware;
use App\Http\Controllers\User\CreateController;
use App\Http\Controllers\User\LoginController;
use App\Http\Controllers\User\ViewController;
use App\Http\Controllers\User\EditController;
use App\Http\Controllers\User\LogoutController;
use App\Http\Controllers\User\DeleteController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\ResetPasswordController;

Route::prefix('v1')->group(function () {
    // USER GROUPED ROUTES
    Route::prefix('user')->group(function () {
        Route::post('create', CreateController::class)->name('user.create');
        Route::post('login', LoginController::class)->name('user.login');
        Route::post('/forgot-password', ForgotPasswordController::class)->name('user.forgot-password');
        Route::post('/reset-password-token', ResetPasswordController::class)->name('user.reset-password-token');

        Route::middleware([JwtMiddleware::class])->group(function () {
            Route::get('/', ViewController::class)->name('user.view');
            Route::put('/edit', EditController::class)->name('user.edit');
            Route::get('/logout', LogoutController::class)->name('user.logout');
            Route::delete('/', DeleteController::class)->name('user.delete');
        });
    });
});
