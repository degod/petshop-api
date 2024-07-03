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
use App\Http\Controllers\Categories\CreateCategoryController;
use App\Http\Controllers\Categories\EditCategoryController;
use App\Http\Controllers\Categories\ViewCategoryController;
use App\Http\Controllers\Categories\ListCategoryController;
use App\Http\Controllers\Categories\DeleteCategoryController;
use App\Http\Controllers\Products\CreateProductController;
use App\Http\Controllers\Products\EditProductController;
use App\Http\Controllers\Products\ListProductController;
use App\Http\Controllers\Products\ViewProductController;
use App\Http\Controllers\Products\DeleteProductController;

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

    // CATEGORY GROUPED ROUTES
    Route::get('/categories', ListCategoryController::class)->name('categories.list');
    Route::prefix('category')->group(function () {
        Route::get('{uuid}', ViewCategoryController::class)->name('categories.view');

        Route::middleware([JwtMiddleware::class])->group(function () {
            Route::post('create', CreateCategoryController::class)->name('categories.create');
            Route::put('{uuid}', EditCategoryController::class)->name('categories.edit');
            Route::delete('{uuid}', DeleteCategoryController::class)->name('categories.delete');
        });
    });

    // PRODUCT GROUPED ROUTES
    Route::get('/products', ListProductController::class)->name('products.list');
    Route::prefix('product')->group(function () {
        Route::get('{uuid}', ViewProductController::class)->name('products.view');

        Route::middleware([JwtMiddleware::class])->group(function () {
            Route::post('create', CreateProductController::class)->name('products.create');
            Route::put('{uuid}', EditProductController::class)->name('products.edit');
            Route::delete('{uuid}', DeleteProductController::class)->name('products.delete');
        });
    });
});
