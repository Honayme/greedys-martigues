<?php

namespace Webkul\MondialRelay\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Injecter le sélecteur de point relais après les méthodes shipping
        Event::listen('bagisto.shop.checkout.onepage.shipping.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('mondialrelay::checkout.point-relais-selector');
        });

        // Injecter la section Mondial Relay après les items de commande
        Event::listen('bagisto.admin.sales.order.left_component.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('mondialrelay::admin.orders.mondial-relay-info');
        });
    }
}
