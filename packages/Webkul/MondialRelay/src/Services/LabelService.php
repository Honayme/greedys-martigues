<?php

namespace Webkul\MondialRelay\Services;

use Webkul\Sales\Models\Order;
use Webkul\MondialRelay\Models\OrderMondialRelay;
use Exception;

class LabelService
{
    public function __construct(
        protected MondialRelayApi $api
    ) {}

    /**
     * Génère l'étiquette pour une commande
     */
    public function generateLabel(int $orderId): array
    {
        $order = Order::with(['addresses', 'items', 'customer'])->findOrFail($orderId);

        // Vérifier que c'est bien une commande Mondial Relay
        if (! str_starts_with($order->shipping_method ?? '', 'mondialrelay_')) {
            throw new Exception("Cette commande n'utilise pas Mondial Relay");
        }

        // Récupérer les données MR
        $mrData = OrderMondialRelay::where('order_id', $orderId)->first();

        if (! $mrData) {
            throw new Exception("Données Mondial Relay introuvables pour cette commande");
        }

        // Récupérer l'adresse du client pour nom/tel/email (passerelle relationnelle)
        $customerAddress = $order->shipping_address;

        if (! $customerAddress) {
            throw new Exception("Adresse client introuvable");
        }

        // Calculer le poids total (en kg)
        $totalWeight = $order->items->sum(function ($item) {
            return ($item->weight ?? 0) * ($item->qty_ordered ?? 0);
        });

        if ($totalWeight <= 0) {
            throw new Exception("Poids total invalide. Vérifiez les poids produits.");
        }

        // Récupérer l'adresse expéditeur depuis la config ou base de données
        $senderData = $this->getSenderData();

        // Préparer les infos destinataire selon le mode de livraison
        $recipientData = $this->getRecipientData($mrData, $customerAddress, $order);

        // Préparer les données pour l'API
        $orderData = [
            'order_id' => $order->id,
            'customer_id' => $order->customer_id ?? '',
            'delivery_mode' => $mrData->delivery_mode,
            'point_relais_id' => $mrData->point_relais_id,
            'weight' => $totalWeight,
            'amount' => $order->grand_total,
            'sender' => $senderData,
            'recipient' => $recipientData,
        ];

        // Appel API
        $result = $this->api->createLabel($orderData);

        // Mise à jour des données MR
        $mrData->update([
            'tracking_number' => $result['tracking_number'],
            'label_url' => $result['label_url'],
        ]);

        return $result;
    }

    /**
     * Prépare les données destinataire selon le mode de livraison
     */
    private function getRecipientData(OrderMondialRelay $mrData, $customerAddress, Order $order): array
    {
        $customerName = $customerAddress->first_name . ' ' . $customerAddress->last_name;
        $customerPhone = $customerAddress->phone ?? '';
        $customerEmail = $order->customer_email;

        // Pour Point Relais et Locker : livraison au point relais
        if ($mrData->delivery_mode === '24R' || $mrData->delivery_mode === '24L') {
            return [
                'name' => $customerName, // Nom du client pour retrait
                'address' => $mrData->point_relais_address ?? '',
                'city' => $mrData->point_relais_city ?? '',
                'postcode' => $mrData->point_relais_postcode ?? '',
                'country' => $mrData->point_relais_country ?? 'FR',
                'phone' => $customerPhone, // Téléphone du client
                'email' => $customerEmail, // Email du client
            ];
        }

        // Pour Domicile : livraison à l'adresse du client
        return [
            'name' => $customerName,
            'address' => $customerAddress->address1,
            'city' => $customerAddress->city,
            'postcode' => $customerAddress->postcode,
            'country' => $customerAddress->country,
            'phone' => $customerPhone,
            'email' => $customerEmail,
        ];
    }

    /**
     * Récupère les données expéditeur
     * TODO: À adapter selon ta config
     */
    private function getSenderData(): array
    {
        return [
            'name' => core()->getConfigData('sales.shipping.origin.store_name') ?? 'Greedy\'s Martigues',
            'address' => core()->getConfigData('sales.shipping.origin.address') ?? '',
            'city' => core()->getConfigData('sales.shipping.origin.city') ?? '',
            'postcode' => core()->getConfigData('sales.shipping.origin.zipcode') ?? '',
            'country' => core()->getConfigData('sales.shipping.origin.country') ?? 'FR',
            'phone' => core()->getConfigData('sales.shipping.origin.contact') ?? '',
            'email' => core()->getConfigData('emails.configure.email_settings.shop_email_from')
                      ?? config('mail.from.address')
                      ?? 'contact@greedyscreation.com',
        ];
    }
}
