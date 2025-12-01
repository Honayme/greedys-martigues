<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        parent::boot();

        // Injecter le champ address2 dans le formulaire checkout
        \Event::listen('bagisto.shop.checkout.onepage.address.form.address.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shop::address-fields.address2-checkout');
        });

        // Injecter le champ address2 dans le formulaire création adresse client
        \Event::listen('bagisto.shop.customers.account.addresses.create_form_controls.street_address.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shop::address-fields.address2-customer');
        });

        // Injecter le champ address2 dans le formulaire édition adresse client
        \Event::listen('bagisto.shop.customers.account.addresses.edit_form_controls.street-addres.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('shop::address-fields.address2-customer-edit');
        });
    }
}
