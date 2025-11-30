<?php

namespace Webkul\MondialRelay\Observers;

use Webkul\Sales\Models\Order;
use Webkul\MondialRelay\Models\OrderMondialRelay;

class OrderObserver
{
    /**
     * Appelé après la création d'une commande
     */
    public function created(Order $order): void
    {
        // Vérifier si c'est une commande Mondial Relay
        if (!str_starts_with($order->shipping_method ?? '', 'mondialrelay_')) {
            return;
        }

        // Déterminer le mode de livraison
        $deliveryMode = $this->getDeliveryMode($order->shipping_method);

        $data = [
            'order_id' => $order->id,
            'delivery_mode' => $deliveryMode,
        ];

        // Récupérer les données du point relais depuis la session ou request
        if ($deliveryMode !== 'HOM') {
            $pointData = session('mondial_relay_selected_point') ?? request()->session()->get('mondial_relay_selected_point');

            if (is_string($pointData)) {
                $pointData = json_decode($pointData, true);
            }

            if ($pointData && is_array($pointData)) {
                $data['point_relais_id'] = $pointData['id'] ?? null;
                $data['point_relais_name'] = $pointData['name'] ?? null;
                $data['point_relais_address'] = $pointData['address'] ?? null;
                $data['point_relais_city'] = $pointData['city'] ?? null;
                $data['point_relais_postcode'] = $pointData['postcode'] ?? null;
                $data['point_relais_country'] = $pointData['country'] ?? null;
            }
        }

        OrderMondialRelay::create($data);

        // Nettoyer la session
        session()->forget('mondial_relay_selected_point');
    }

    /**
     * Détermine le code service Mondial Relay depuis le shipping method
     */
    private function getDeliveryMode(string $shippingMethod): string
    {
        if (str_contains($shippingMethod, 'point_relais')) {
            return '24R';  // Point Relais standard
        } elseif (str_contains($shippingMethod, 'locker')) {
            return '24R';  // Locker (même code que Point Relais, distingué par Location ID)
        } elseif (str_contains($shippingMethod, 'domicile')) {
            return 'HOM';  // Livraison domicile
        }

        return '24R'; // Défaut
    }
}
