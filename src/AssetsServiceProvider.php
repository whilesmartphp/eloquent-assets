<?php

namespace Whilesmart\Assets;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AssetsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/assets.php', 'assets');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../config/assets.php' => config_path('assets.php'),
        ], 'assets-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'assets-migrations');

        if (config('assets.register_routes', true)) {
            Route::middleware(config('assets.route_middleware', ['api', 'auth:sanctum']))
                ->prefix(config('assets.route_prefix', 'api'))
                ->group(__DIR__.'/../routes/api.php');
        }
    }
}
