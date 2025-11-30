<?php

namespace Webkul\MondialRelay\Services;

use Exception;
use Webkul\MondialRelay\Models\OrderMondialRelay;
use Webkul\Sales\Models\Order;

class LabelService
{
    public function __construct(
        protected MondialRelayRestApi $restApi
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
            throw new Exception('Données Mondial Relay introuvables pour cette commande');
        }

        // Récupérer l'adresse du client (shipping ou billing si shipping est vide)
        $customerAddress = $order->shipping_address;

        if (! $customerAddress || empty($customerAddress->address)) {
            $customerAddress = $order->billing_address;
        }

        if (! $customerAddress) {
            throw new Exception('Adresse client introuvable');
        }

        // Calculer le poids total (en kg)
        $totalWeight = $order->items->sum(function ($item) {
            return ($item->weight ?? 0) * ($item->qty_ordered ?? 0);
        });

        if ($totalWeight <= 0) {
            throw new Exception('Poids total invalide. Vérifiez les poids produits.');
        }

        // Récupérer l'adresse expéditeur brute
        $rawSenderData = $this->getSenderData();

        // Préparer les infos destinataire brutes selon le mode de livraison
        $rawRecipientData = $this->getRecipientData($mrData, $customerAddress, $order);

        // Préparer les adresses brutes (le formatage strict se fera dans MondialRelayRestApi)
        $senderData = $this->prepareAddressData($rawSenderData);
        $recipientData = $this->prepareAddressData($rawRecipientData);

        // Préparer les données pour l'API REST V2
        $shipmentData = [
            'order_id'             => (string) $order->id,
            'customer_no'          => $order->customer_id ? (string) $order->customer_id : '',
            'delivery_mode'        => $mrData->delivery_mode,
            'point_relais_id'      => $mrData->point_relais_id,
            'weight'               => $totalWeight,
            'shipment_value'       => $order->grand_total,
            'content'              => 'Produits e-commerce',
            'delivery_instruction' => '',
            'sender'               => $senderData,
            'recipient'            => $recipientData,
        ];

        // Log détaillé pour debug
        \Log::info('MR REST API V2 - Label Creation Data', [
            'api_version'          => 'V2 REST',
            'order_id'             => $order->id,
            'shipping_method'      => $order->shipping_method,
            'customer_address_raw' => [
                'address1' => $customerAddress->address1 ?? 'NULL',
                'address2' => $customerAddress->address2 ?? 'NULL',
                'city'     => $customerAddress->city ?? 'NULL',
                'postcode' => $customerAddress->postcode ?? 'NULL',
                'country'  => $customerAddress->country ?? 'NULL',
                'phone'    => $customerAddress->phone ?? 'NULL',
            ],
            'mr_data' => [
                'delivery_mode'         => $mrData->delivery_mode,
                'point_relais_id'       => $mrData->point_relais_id,
                'point_relais_address'  => $mrData->point_relais_address ?? 'NULL',
                'point_relais_city'     => $mrData->point_relais_city ?? 'NULL',
                'point_relais_postcode' => $mrData->point_relais_postcode ?? 'NULL',
            ],
            'shipment_data_prepared' => $shipmentData,
            'total_weight_kg'        => $totalWeight,
            'total_weight_grams'     => ($totalWeight * 1000),
            'total_amount'           => $order->grand_total,
        ]);

        // Appel API REST V2
        $result = $this->restApi->createShipment($shipmentData);

        \Log::info('MR REST API V2 - Label Created Successfully', [
            'order_id'        => $order->id,
            'tracking_number' => $result['tracking_number'],
            'label_url'       => $result['label_url'],
        ]);

        // Mise à jour des données MR
        $mrData->update([
            'tracking_number' => $result['tracking_number'],
            'label_url'       => $result['label_url'],
        ]);

        return $result;
    }

    /**
     * Prépare les données destinataire selon le mode de livraison
     */
    private function getRecipientData(OrderMondialRelay $mrData, $customerAddress, Order $order): array
    {
        $customerName = $customerAddress->first_name.' '.$customerAddress->last_name;
        $customerPhone = $customerAddress->phone ?? '';
        $customerEmail = $order->customer_email;

        // Pour Point Relais et Locker : livraison au point relais (les deux utilisent le code 24R)
        if ($mrData->delivery_mode === '24R') {
            return [
                'name'     => $customerName, // Nom du client pour retrait
                'address'  => $mrData->point_relais_address ?? '',
                'address2' => '',
                'city'     => $mrData->point_relais_city ?? '',
                'postcode' => $mrData->point_relais_postcode ?? '',
                'country'  => $mrData->point_relais_country ?? 'FR',
                'phone'    => $customerPhone, // Téléphone du client
                'email'    => $customerEmail, // Email du client
            ];
        }

        // Pour Domicile : livraison à l'adresse du client
        return [
            'name'     => $customerName,
            'address'  => $customerAddress->address1 ?? $customerAddress->address ?? '',
            'address2' => $customerAddress->address2 ?? '',
            'city'     => $customerAddress->city ?? '',
            'postcode' => $customerAddress->postcode ?? '',
            'country'  => $customerAddress->country ?? 'FR',
            'phone'    => $customerPhone,
            'email'    => $customerEmail,
        ];
    }

    /**
     * Prépare les données d'adresse brutes (le formatage strict se fait dans MondialRelayRestApi)
     * @param array $addressData ['name', 'address', 'address2', 'city', 'postcode', 'country', 'phone', 'email']
     * @return array
     */
    private function prepareAddressData(array $addressData): array
    {
        // Séparation nom/prénom simple
        $fullName = trim($addressData['name'] ?? '');
        $parts = explode(' ', $fullName);

        if (count($parts) > 1) {
            $lastname = array_pop($parts);
            $firstname = implode(' ', $parts);
        } else {
            $firstname = $fullName;
            $lastname = '.';
        }

        // Préparer les données brutes - le formatage strict se fera dans MondialRelayRestApi
        return [
            'Title'       => '',
            'Firstname'   => $firstname,
            'Lastname'    => $lastname,
            'Streetname'  => trim(($addressData['address'] ?? '') . ' ' . ($addressData['address2'] ?? '')),
            'HouseNo'     => '',
            'City'        => $addressData['city'] ?? '',
            'PostCode'    => $addressData['postcode'] ?? '',
            'CountryCode' => strtoupper($addressData['country'] ?? 'FR'),
            'AddressAdd1' => '',
            'AddressAdd2' => '',
            'AddressAdd3' => '',
            'PhoneNo'     => $addressData['phone'] ?? '',
            'MobileNo'    => $addressData['phone'] ?? '',
            'Email'       => $addressData['email'] ?? '',
        ];
    }

    /**
     * Récupère les données expéditeur depuis la configuration Bagisto
     *
     * @throws Exception
     */
    private function getSenderData(): array
    {
        $phone = core()->getConfigData('sales.shipping.origin.contact');

        if (empty($phone)) {
            throw new Exception('Le numéro de téléphone expéditeur doit être configuré dans Admin > Configuration > Sales > Shipping Settings > Origin > Contact Number');
        }

        $address = core()->getConfigData('sales.shipping.origin.address');
        $city = core()->getConfigData('sales.shipping.origin.city');
        $postcode = core()->getConfigData('sales.shipping.origin.zipcode');

        if (empty($address) || empty($city) || empty($postcode)) {
            throw new Exception('L\'adresse expéditeur est incomplète. Vérifiez Admin > Configuration > Sales > Shipping Settings > Origin');
        }

        return [
            'name'     => core()->getConfigData('sales.shipping.origin.store_name') ?? 'Greedy\'s Martigues',
            'address'  => $address,
            'address2' => '',
            'city'     => $city,
            'postcode' => $postcode,
            'country'  => core()->getConfigData('sales.shipping.origin.country') ?? 'FR',
            'phone'    => $phone,
            'email'    => core()->getConfigData('emails.configure.email_settings.shop_email_from')
                      ?? config('mail.from.address')
                      ?? 'contact@greedyscreation.com',
        ];
    }
}
