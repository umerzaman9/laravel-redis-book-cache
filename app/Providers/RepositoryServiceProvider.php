<?php

namespace App\Providers;

use App\Repositories\Interface\AuthInterface;
use App\Repositories\Interface\BookInterface;
use App\Repositories\Repository\AuthRepository;
use App\Repositories\Repository\BookRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(AuthInterface::class, AuthRepository::class);
        $this->app->bind(BookInterface::class, BookRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
