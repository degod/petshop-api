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
use App\Http\Controllers\MainPage\PromotionsController;
use App\Http\Controllers\MainPage\BlogPostsController;
use App\Http\Controllers\MainPage\BlogPostController;

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

    // MAIN-PAGE GROUPED ROUTES
    Route::prefix('main')->group(function () {
        Route::get('/promotions', PromotionsController::class)->name('main.promotions');
        Route::get('/blog', BlogPostsController::class)->name('main.blog');
        Route::get('/blog/{uuid}', BlogPostController::class)->name('main.blog.view');
    });
});
