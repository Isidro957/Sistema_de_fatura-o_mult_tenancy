<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Namespace para os controllers.
     * Você pode remover se estiver usando PHP 8+ e rotas sem namespace.
     */
    protected $namespace = 'App\\Http\\Controllers';

    /**
     * Caminho para a página inicial (home) do Laravel.
     */
    public const HOME = '/home';

    /**
     * Registrar rotas do aplicativo.
     */
    public function boot(): void
    {
        parent::boot();

        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapTenantRoutes(); // Registrando tenant.php
    }

    /**
     * Rotas web (normalmente pages blade, session cookies, etc.)
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Rotas API (sem estado de sessão, normalmente JSON)
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * Rotas do tenant (multi-tenant)
     */
    protected function mapTenantRoutes(): void
    {
        Route::prefix('tenant') // Prefixo opcional
            ->middleware(['web']) // ou 'api' se preferir JSON puro
            ->namespace($this->namespace)
            ->group(base_path('routes/tenant.php'));
    }
}
