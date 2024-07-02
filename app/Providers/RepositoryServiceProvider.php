<?php

namespace App\Providers;

use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\PasswordResets\PasswordResetRepository;
use App\Repositories\PasswordResets\PasswordResetRepositoryInterface;
use App\Repositories\Files\FileRepository;
use App\Repositories\Files\FileRepositoryInterface;
use App\Repositories\Promotions\PromotionRepository;
use App\Repositories\Promotions\PromotionRepositoryInterface;
use App\Repositories\Posts\PostRepository;
use App\Repositories\Posts\PostRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
        $this->app->bind(PromotionRepositoryInterface::class, PromotionRepository::class);
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
