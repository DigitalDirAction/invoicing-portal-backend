<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\PasswordResetRepository;
use App\Interfaces\PasswordResetRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(PasswordResetRepositoryInterface::class, PasswordResetRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
