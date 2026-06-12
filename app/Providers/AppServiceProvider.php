<?php

namespace App\Providers;

use App\application\Authenticated\UserAuthenticated;
use App\application\Produto\ProdutoService;
use App\Contracts\Repository\ProdutoRepositoryContract;
use App\Contracts\Repository\UserRepositoryContract;
use App\Contracts\Services\ProdutoServiceContract;
use App\Contracts\Services\UserAuthContract;
use App\Infrastructure\Repository\Produto\ProdutoRepository;
use App\Infrastructure\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserAuthContract::class, UserAuthenticated::class);
        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
        $this->app->bind(ProdutoServiceContract::class, ProdutoService::class);
        $this->app->bind(ProdutoRepositoryContract::class, ProdutoRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
