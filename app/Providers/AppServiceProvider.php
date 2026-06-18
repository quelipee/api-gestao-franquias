<?php

namespace App\Providers;

use App\application\Authenticated\UserAuthenticated;
use App\application\Estoque\EstoqueService;
use App\application\Estoque\MovimentacaoEstoqueService;
use App\application\Produto\ProdutoService;
use App\application\Unidade\UnidadeService;
use App\application\UnidadeProduto\UnidadeProdutoService;
use App\Contracts\Repository\EstoqueRepositoryContract;
use App\Contracts\Repository\ProdutoRepositoryContract;
use App\Contracts\Repository\UnidadeRepositoryContract;
use App\Contracts\Repository\UserRepositoryContract;
use App\Contracts\Services\EstoqueServiceContract;
use App\Contracts\Services\MovimentacaoEstoqueServiceContract;
use App\Contracts\Services\ProdutoServiceContract;
use App\Contracts\Services\UnidadeProdutoServiceContract;
use App\Contracts\Services\UnidadeServiceContract;
use App\Contracts\Services\UserAuthContract;
use App\Infrastructure\Repository\Estoque\EstoqueRepository;
use App\Infrastructure\Repository\Produto\ProdutoRepository;
use App\Infrastructure\Repository\Unidade\UnidadeRepository;
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
        $this->app->bind(UnidadeServiceContract::class, UnidadeService::class);
        $this->app->bind(UnidadeRepositoryContract::class, UnidadeRepository::class);
        $this->app->bind(UnidadeProdutoServiceContract::class, UnidadeProdutoService::class);
        $this->app->bind(EstoqueServiceContract::class, EstoqueService::class);
        $this->app->bind(EstoqueRepositoryContract::class, EstoqueRepository::class);
        $this->app->bind(MovimentacaoEstoqueServiceContract::class, MovimentacaoEstoqueService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
