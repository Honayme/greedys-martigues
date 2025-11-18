<?php

namespace Webkul\MondialRelay\Carriers;

use Webkul\Checkout\Models\Cart;
use Webkul\Shipping\Carriers\AbstractShipping;
use Webkul\Shipping\Facades\Shipping;

class MondialRelay extends AbstractShipping
{
    protected $code = 'mondialrelay';

    /**
     * Calcule les tarifs disponibles
     *
     * @return array|false
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $cart = $this->getCart();

        if (! $cart) {
            return false;
        }

        $totalWeight = $this->getTotalWeight($cart);

        // Mondial Relay limite à 30kg max
        if ($totalWeight > 30) {
            return false;
        }

        $rates = [];

        // Point Relais
        if ($this->getConfigData('enable_point_relais')) {
            $rate = new \Webkul\Checkout\Models\CartShippingRate;
            $rate->carrier = $this->getCode();
            $rate->carrier_title = $this->getConfigData('title');
            $rate->method = $this->getCode() . '_point_relais';
            $rate->method_title = 'Point Relais';
            $rate->method_description = 'Retrait en Point Relais® (délai 2-3 jours)';
            $rate->price = core()->convertPrice($this->calculatePrice($totalWeight, 'point_relais'));
            $rate->base_price = $this->calculatePrice($totalWeight, 'point_relais');

            $rates[] = $rate;
        }

        // Locker
        if ($this->getConfigData('enable_locker')) {
            $rate = new \Webkul\Checkout\Models\CartShippingRate;
            $rate->carrier = $this->getCode();
            $rate->carrier_title = $this->getConfigData('title');
            $rate->method = $this->getCode() . '_locker';
            $rate->method_title = 'Consigne automatique (Locker)';
            $rate->method_description = 'Retrait en consigne 24h/24 (délai 2-3 jours)';
            $rate->price = core()->convertPrice($this->calculatePrice($totalWeight, 'locker'));
            $rate->base_price = $this->calculatePrice($totalWeight, 'locker');

            $rates[] = $rate;
        }

        // Domicile
        if ($this->getConfigData('enable_domicile')) {
            $rate = new \Webkul\Checkout\Models\CartShippingRate;
            $rate->carrier = $this->getCode();
            $rate->carrier_title = $this->getConfigData('title');
            $rate->method = $this->getCode() . '_domicile';
            $rate->method_title = 'Livraison à domicile';
            $rate->method_description = 'Livraison à votre adresse (délai 2-4 jours)';
            $rate->price = core()->convertPrice($this->calculatePrice($totalWeight, 'domicile'));
            $rate->base_price = $this->calculatePrice($totalWeight, 'domicile');

            $rates[] = $rate;
        }

        if (empty($rates)) {
            return false;
        }

        return $rates;
    }

    /**
     * Récupère le panier actif
     */
    private function getCart(): ?Cart
    {
        return cart()->getCart();
    }

    /**
     * Calcule le poids total du panier (en kg)
     */
    private function getTotalWeight(Cart $cart): float
    {
        $totalWeight = 0;

        foreach ($cart->items as $item) {
            $weight = $item->product->weight ?? 0;
            $totalWeight += $weight * $item->quantity;
        }

        return $totalWeight;
    }

    /**
     * Calcule le prix selon le poids et le type de livraison
     */
    private function calculatePrice(float $weightKg, string $deliveryMode): float
    {
        $weightGrams = $weightKg * 1000;

        $pricing = config("carriers.mondialrelay.pricing.{$deliveryMode}");

        // Locker: tarif fixe
        if ($deliveryMode === 'locker') {
            return $pricing;
        }

        // Point Relais / Domicile: tarif par tranche de poids
        if (is_array($pricing)) {
            if ($weightGrams <= 250) {
                return $pricing[0];
            } elseif ($weightGrams <= 500) {
                return $pricing[250];
            } elseif ($weightGrams <= 1000) {
                return $pricing[500];
            } else {
                // Au-delà de 1kg, prendre le dernier tarif
                return $pricing[1000];
            }
        }

        // Fallback
        return $this->getConfigData('default_rate') ?? 5.00;
    }

    /**
     * Vérifie si le shipping method est disponible
     */
    public function isAvailable(): bool
    {
        // Vérifier que le module est actif
        if (! $this->getConfigData('active')) {
            return false;
        }

        // Vérifier que les credentials API sont configurés
        if (empty($this->getConfigData('code_enseigne'))
            || empty($this->getConfigData('private_key'))
            || empty($this->getConfigData('code_marque'))) {
            return false;
        }

        return true;
    }
}
