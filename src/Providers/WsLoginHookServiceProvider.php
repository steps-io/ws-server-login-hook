<?php
namespace  Steps\WsLoginHook\Providers;

use Illuminate\Support\ServiceProvider;

class WsLoginHookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/ws-login-hook.php' => config_path('ws-login-hook.php'),
        ], 'config');

        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'ws-login-hook');

        // Routes
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');

        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/ws-login-hook.php', 'ws-login-hook'
        );
    }
}
