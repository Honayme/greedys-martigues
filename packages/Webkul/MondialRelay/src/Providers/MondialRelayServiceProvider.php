<?php

namespace Webkul\MondialRelay\Providers;

use Illuminate\Support\ServiceProvider;
use Webkul\Sales\Models\Order;
use Webkul\MondialRelay\Observers\OrderObserver;

class MondialRelayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/carriers.php', 'carriers'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        // Enregistrer l'EventServiceProvider
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__) . '/Database/Migrations');

        $this->loadRoutesFrom(dirname(__DIR__) . '/Http/routes.php');

        $this->loadViewsFrom(dirname(__DIR__) . '/Resources/views', 'mondialrelay');

        $this->publishes([
            dirname(__DIR__) . '/Resources/views' => resource_path('themes/default/views/mondialrelay'),
        ], 'mondialrelay-views');

        // Enregistrer l'Observer Order
        Order::observe(OrderObserver::class);
    }
}
