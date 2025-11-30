<?php

namespace Webkul\MondialRelay\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class MondialRelayRestApi
{
    private string $apiUrl;

    private string $login;

    private string $password;

    private string $brandId;

    public function __construct()
    {
        $this->apiUrl = core()->getConfigData('sales.carriers.mondialrelay.api_v2_url')
            ?? 'https://connect-api.mondialrelay.com/api/shipment';
        $this->login = core()->getConfigData('sales.carriers.mondialrelay.api_v2_login') ?? '';
        $this->password = core()->getConfigData('sales.carriers.mondialrelay.api_v2_password') ?? '';
        $this->brandId = core()->getConfigData('sales.carriers.mondialrelay.api_v2_brand_id') ?? '';
    }

    /**
     * Crée une expédition et génère l'étiquette
     *
     * @param  array  $shipmentData  Données de l'expédition
     * @return array ['tracking_number', 'label_url']
     *
     * @throws Exception
     */
    public function createShipment(array $shipmentData): array
    {
        // Validation des credentials
        if (empty($this->login) || empty($this->password) || empty($this->brandId)) {
            throw new Exception('Credentials API V2 REST non configurés');
        }

        // Validation du téléphone
        $this->validatePhone($shipmentData['sender']['MobileNo'], 'expéditeur');
        $this->validatePhone($shipmentData['recipient']['MobileNo'], 'destinataire');

        // Validation du poids
        $this->validateWeight($shipmentData['weight']);

        // Construction du XML
        $xml = $this->buildShipmentXml($shipmentData);

        // Log de la requête XML
        \Log::info('MR REST API V2 - Request XML', [
            'url'     => $this->apiUrl,
            'xml'     => $xml,
            'headers' => [
                'Accept'       => 'application/xml',
                'Content-Type' => 'text/xml; charset=utf-8',
            ],
        ]);

        // Appel API
        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/xml',
                'Content-Type' => 'text/xml; charset=utf-8',
            ])->withBody($xml, 'text/xml')->post($this->apiUrl);

            // Log de la réponse
            \Log::info('MR REST API V2 - Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if (! $response->successful()) {
                throw new Exception('Erreur HTTP '.$response->status().': '.$response->body());
            }

            // Parser la réponse XML
            return $this->parseResponse($response->body());

        } catch (\Exception $e) {
            \Log::error('MR REST API V2 - Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            throw new Exception('Erreur appel API REST: '.$e->getMessage());
        }
    }

    /**
     * Valide un numéro de téléphone français
     *
     * @throws Exception
     */
    private function validatePhone(string $phone, string $field): void
    {
        if (empty($phone)) {
            throw new Exception("Le numéro de téléphone du {$field} est obligatoire");
        }

        // Regex France selon doc Mondial Relay
        $regex = '/^((00|\+)?33|0)[0-9]{9}$/';

        $cleanPhone = preg_replace('/[\s\.\-]/', '', $phone);

        if (! preg_match($regex, $cleanPhone)) {
            throw new Exception("Format téléphone {$field} invalide. Format attendu: 0XXXXXXXXX ou +33XXXXXXXXX");
        }
    }

    /**
     * Valide le poids de l'expédition
     *
     * @param  float  $weightKg  Poids en kilogrammes
     *
     * @throws Exception
     */
    private function validateWeight(float $weightKg): void
    {
        $weightGrams = $weightKg * 1000;

        if ($weightGrams < 10) {
            throw new Exception('Poids minimum: 10 grammes');
        }

        if ($weightKg > 30) {
            throw new Exception('Poids maximum Mondial Relay: 30 kg');
        }
    }

    /**
     * Formate un champ selon le format UPPERCASE avec caractères autorisés
     * Format: ^[0-9A-Z_\-\'., /]{0,max}$
     * Utilisé pour: Streetname, Content, OrderNo, AddressAdd1/2/3, DeliveryInstruction
     */
    private function formatFieldUppercase(string $value, int $maxLength): string
    {
        if (empty($value)) {
            return '';
        }

        // Supprimer accents
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        // Convertir en majuscules
        $value = strtoupper($value);

        // Garder uniquement les caractères autorisés: 0-9A-Z_\-\'., /
        $value = preg_replace('/[^0-9A-Z_\-\'., \/]/', '', $value);

        // Remplacer espaces multiples par un seul
        $value = preg_replace('/\s+/', ' ', $value);

        // Tronquer et trim
        return trim(substr($value, 0, $maxLength));
    }

    /**
     * Formate un champ selon le format mixte (a-zA-Z)
     * Format réel: ^[A-Za-z_\-'\s]{min,max}$ (avec espaces)
     * Utilisé pour: City
     */
    private function formatFieldMixedCase(string $value, int $minLength, int $maxLength): string
    {
        if (empty($value)) {
            return '';
        }

        // Supprimer accents
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        // Garder uniquement les caractères autorisés: A-Za-z_\-' et espaces
        $value = preg_replace('/[^A-Za-z_\-\'\s]/', '', $value);

        // Remplacer espaces multiples par un seul
        $value = preg_replace('/\s+/', ' ', $value);

        // Tronquer
        $value = substr($value, 0, $maxLength);

        return trim($value);
    }

    /**
     * Formate un code postal (accepte lettres ET chiffres)
     * Format réel: ^[0-9A-Za-z\-\s]{2,10}$
     * Utilisé pour: PostCode
     */
    private function formatPostCode(string $value, int $maxLength = 10): string
    {
        if (empty($value)) {
            return '';
        }

        // Garder uniquement chiffres, lettres, tirets et espaces
        $value = preg_replace('/[^0-9A-Za-z\-\s]/', '', $value);

        // Supprimer espaces multiples
        $value = preg_replace('/\s+/', ' ', $value);

        // Tronquer
        $value = substr(trim($value), 0, $maxLength);

        return $value;
    }

    /**
     * Formate les noms (Title, Firstname, Lastname) avec contrainte combinée max 32 caractères
     *
     * @param array $names ['title' => '', 'firstname' => '', 'lastname' => '']
     * @return array Noms formatés
     */
    private function formatNames(array $names): array
    {
        $title = $this->formatFieldUppercase($names['title'] ?? '', 10);
        $firstname = $this->formatFieldUppercase($names['firstname'] ?? '', 20);
        $lastname = $this->formatFieldUppercase($names['lastname'] ?? '', 20);

        // Vérifier contrainte combinée max 32 caractères
        $totalLength = strlen($title) + strlen($firstname) + strlen($lastname);

        if ($totalLength > 32) {
            // Stratégie: garder firstname et lastname, réduire ou supprimer title
            $title = '';
            $totalLength = strlen($firstname) + strlen($lastname);

            // Si encore trop long, tronquer lastname puis firstname
            if ($totalLength > 32) {
                $available = 32 - strlen($firstname);
                if ($available > 0) {
                    $lastname = substr($lastname, 0, $available);
                } else {
                    $firstname = substr($firstname, 0, 20);
                    $lastname = substr($lastname, 0, 12);
                }
            }
        }

        return [
            'title' => $title,
            'firstname' => $firstname,
            'lastname' => $lastname,
        ];
    }

    /**
     * Formate une adresse complète selon les règles Mondial Relay
     *
     * @param array $address Données brutes de l'adresse
     * @return array Adresse formatée et validée
     */
    private function formatAddress(array $address): array
    {
        // Formater les noms avec contrainte 32 caractères
        $names = $this->formatNames([
            'title' => $address['Title'] ?? '',
            'firstname' => $address['Firstname'] ?? '',
            'lastname' => $address['Lastname'] ?? '',
        ]);

        // Séparer numéro de rue et nom de rue si possible
        $streetname = $address['Streetname'] ?? '';
        $houseNo = '';

        // Tentative d'extraction du numéro (pattern: commence par des chiffres)
        if (preg_match('/^(\d+[a-zA-Z]?)\s+(.+)$/', $streetname, $matches)) {
            $houseNo = $matches[1];
            $streetname = $matches[2];
        }

        return [
            'Title' => $names['title'],
            'Firstname' => $names['firstname'],
            'Lastname' => $names['lastname'],
            'Streetname' => $this->formatFieldUppercase($streetname, 40),
            'HouseNo' => $this->formatFieldUppercase($houseNo, 10),
            'CountryCode' => strtoupper(substr($address['CountryCode'] ?? 'FR', 0, 2)),
            'PostCode' => $this->formatPostCode($address['PostCode'] ?? '', 10),
            'City' => $this->formatFieldMixedCase($address['City'] ?? '', 2, 30),
            'AddressAdd1' => $this->formatFieldUppercase($address['AddressAdd1'] ?? '', 30),
            'AddressAdd2' => $this->formatFieldUppercase($address['AddressAdd2'] ?? '', 30),
            'AddressAdd3' => $this->formatFieldUppercase($address['AddressAdd3'] ?? '', 30),
            'PhoneNo' => $this->formatPhone($address['PhoneNo'] ?? ''),
            'MobileNo' => $this->formatPhone($address['MobileNo'] ?? ''),
            'Email' => substr($address['Email'] ?? '', 0, 70),
        ];
    }

    /**
     * Formate un téléphone pour l'API (format français local 0XXXXXXXXX)
     */
    private function formatPhone(string $phone): string
    {
        // Nettoyer le numéro
        $phone = preg_replace('/[\s\.\-]/', '', $phone);

        // Si commence par +33, convertir en format 0X
        if (str_starts_with($phone, '+33')) {
            $phone = '0'.substr($phone, 3);
        }
        // Si commence par 33 (sans +), convertir en format 0X
        elseif (str_starts_with($phone, '33') && strlen($phone) == 11) {
            $phone = '0'.substr($phone, 2);
        }
        // Si ne commence pas par 0, ajouter le 0
        elseif (! str_starts_with($phone, '0') && strlen($phone) == 9) {
            $phone = '0'.$phone;
        }

        return $phone;
    }

    /**
     * Construit le XML de la requête ShipmentCreationRequest
     * Conforme à la doc Web Service Dual Carrier v2.7.1
     */
    private function buildShipmentXml(array $data): string
    {
        $labelFormat = core()->getConfigData('sales.carriers.mondialrelay.label_format') ?? '10x15';
        $weightGrams = (int) ($data['weight'] * 1000);

        // Point Relais et Locker utilisent tous les deux le code 24R (vs HOM pour domicile)
        $isPointRelais = in_array($data['delivery_mode'], ['24R', '24L', 'XOH']);

        // Formater les adresses avec les règles strictes
        $sender = $this->formatAddress($data['sender']);
        $recipient = $this->formatAddress($data['recipient']);

        // Formatage Location ID (Ex: FR-017021)
        $locationId = '';
        if ($isPointRelais && ! empty($data['point_relais_id'])) {
            $countryCode = strtoupper($recipient['CountryCode']);
            $cleanId = str_replace($countryCode.'-', '', $data['point_relais_id']);
            $locationId = $countryCode.'-'.$cleanId;
        }

        // Formater OrderNo (max 15, format ^[0-9A-Z_-]{0,15}$)
        $orderNo = $this->formatFieldUppercase($data['order_id'], 15);

        // Formater Content (max 40, format ^[0-9A-Z_\-\'., /]{0,40}$)
        $content = $this->formatFieldUppercase($data['content'] ?? 'Produits e-commerce', 40);

        // CustomerNo optionnel (max 9, format ^[0-9A-Z]{0,9}$)
        $customerNo = $this->formatFieldUppercase($data['customer_no'] ?? '', 9);

        // ShipmentValue optionnel
        $shipmentValue = isset($data['shipment_value']) ? (float) $data['shipment_value'] : 0;

        // DeliveryInstruction optionnel (max 30)
        $deliveryInstruction = $this->formatFieldUppercase($data['delivery_instruction'] ?? '', 30);

        // Construire le XML
        $xml = '<?xml version="1.0" encoding="utf-8"?>
<ShipmentCreationRequest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.example.org/Request">
    <Context>
        <Login>'.$this->xmlEscape($this->login).'</Login>
        <Password><![CDATA['.$this->password.']]></Password>
        <CustomerId>'.$this->xmlEscape($this->brandId).'</CustomerId>
        <Culture>fr-FR</Culture>
        <VersionAPI>1.0</VersionAPI>
    </Context>
    <OutputOptions>
        <OutputFormat>'.$this->xmlEscape($labelFormat).'</OutputFormat>
        <OutputType>PdfUrl</OutputType>
    </OutputOptions>
    <ShipmentsList>
        <Shipment>
            <OrderNo>'.$this->xmlEscape($orderNo).'</OrderNo>';

        // CustomerNo optionnel
        if (!empty($customerNo)) {
            $xml .= '
            <CustomerNo>'.$this->xmlEscape($customerNo).'</CustomerNo>';
        }

        $xml .= '
            <ParcelCount>1</ParcelCount>';

        // ShipmentValue optionnel mais recommandé
        if ($shipmentValue > 0) {
            $xml .= '
            <ShipmentValue Amount="'.number_format($shipmentValue, 2, '.', '').'" Currency="EUR" />';
        }

        $xml .= '
            <DeliveryMode Mode="'.$this->xmlEscape($data['delivery_mode']).'" Location="'.$this->xmlEscape($locationId).'" />
            <CollectionMode Mode="CCC" Location="" />

            <Parcels>
                <Parcel>
                    <Content>'.$this->xmlEscape($content).'</Content>
                    <Weight Value="'.$weightGrams.'" Unit="gr" />
                </Parcel>
            </Parcels>';

        // DeliveryInstruction optionnel
        if (!empty($deliveryInstruction)) {
            $xml .= '
            <DeliveryInstruction>'.$this->xmlEscape($deliveryInstruction).'</DeliveryInstruction>';
        }

        // Sender Address
        $xml .= '

            <Sender>
                <Address>';

        if (!empty($sender['Title'])) {
            $xml .= '
                    <Title>'.$this->xmlEscape($sender['Title']).'</Title>';
        }

        $xml .= '
                    <Firstname>'.$this->xmlEscape($sender['Firstname']).'</Firstname>
                    <Lastname>'.$this->xmlEscape($sender['Lastname']).'</Lastname>
                    <Streetname>'.$this->xmlEscape($sender['Streetname']).'</Streetname>';

        if (!empty($sender['HouseNo'])) {
            $xml .= '
                    <HouseNo>'.$this->xmlEscape($sender['HouseNo']).'</HouseNo>';
        }

        $xml .= '
                    <CountryCode>'.$this->xmlEscape($sender['CountryCode']).'</CountryCode>
                    <PostCode>'.$this->xmlEscape($sender['PostCode']).'</PostCode>
                    <City>'.$this->xmlEscape($sender['City']).'</City>';

        if (!empty($sender['AddressAdd1'])) {
            $xml .= '
                    <AddressAdd1>'.$this->xmlEscape($sender['AddressAdd1']).'</AddressAdd1>';
        }
        if (!empty($sender['AddressAdd2'])) {
            $xml .= '
                    <AddressAdd2>'.$this->xmlEscape($sender['AddressAdd2']).'</AddressAdd2>';
        }
        if (!empty($sender['AddressAdd3'])) {
            $xml .= '
                    <AddressAdd3>'.$this->xmlEscape($sender['AddressAdd3']).'</AddressAdd3>';
        }
        if (!empty($sender['PhoneNo'])) {
            $xml .= '
                    <PhoneNo>'.$this->xmlEscape($sender['PhoneNo']).'</PhoneNo>';
        }

        $xml .= '
                    <MobileNo>'.$this->xmlEscape($sender['MobileNo']).'</MobileNo>
                    <Email>'.$this->xmlEscape($sender['Email']).'</Email>
                </Address>
            </Sender>

            <Recipient>
                <Address>';

        if (!empty($recipient['Title'])) {
            $xml .= '
                    <Title>'.$this->xmlEscape($recipient['Title']).'</Title>';
        }

        $xml .= '
                    <Firstname>'.$this->xmlEscape($recipient['Firstname']).'</Firstname>
                    <Lastname>'.$this->xmlEscape($recipient['Lastname']).'</Lastname>
                    <Streetname>'.$this->xmlEscape($recipient['Streetname']).'</Streetname>';

        if (!empty($recipient['HouseNo'])) {
            $xml .= '
                    <HouseNo>'.$this->xmlEscape($recipient['HouseNo']).'</HouseNo>';
        }

        $xml .= '
                    <CountryCode>'.$this->xmlEscape($recipient['CountryCode']).'</CountryCode>
                    <PostCode>'.$this->xmlEscape($recipient['PostCode']).'</PostCode>
                    <City>'.$this->xmlEscape($recipient['City']).'</City>';

        if (!empty($recipient['AddressAdd1'])) {
            $xml .= '
                    <AddressAdd1>'.$this->xmlEscape($recipient['AddressAdd1']).'</AddressAdd1>';
        }
        if (!empty($recipient['AddressAdd2'])) {
            $xml .= '
                    <AddressAdd2>'.$this->xmlEscape($recipient['AddressAdd2']).'</AddressAdd2>';
        }
        if (!empty($recipient['AddressAdd3'])) {
            $xml .= '
                    <AddressAdd3>'.$this->xmlEscape($recipient['AddressAdd3']).'</AddressAdd3>';
        }
        if (!empty($recipient['PhoneNo'])) {
            $xml .= '
                    <PhoneNo>'.$this->xmlEscape($recipient['PhoneNo']).'</PhoneNo>';
        }

        $xml .= '
                    <MobileNo>'.$this->xmlEscape($recipient['MobileNo']).'</MobileNo>
                    <Email>'.$this->xmlEscape($recipient['Email']).'</Email>
                </Address>
            </Recipient>
        </Shipment>
    </ShipmentsList>
</ShipmentCreationRequest>';

        return $xml;
    }

    /**
     * Échappe les caractères XML
     */
    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1, 'UTF-8');
    }

    /**
     * Parse la réponse XML de l'API
     *
     * @throws Exception
     */
    private function parseResponse(string $xmlResponse): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlResponse);

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new Exception('Erreur parsing XML: '.print_r($errors, true));
        }

        // Vérifier les erreurs dans StatusList
        if (isset($xml->StatusList) && isset($xml->StatusList->Status)) {
            $status = $xml->StatusList->Status;
            $attributes = $status->attributes();
            $code = (string) ($attributes->Code ?? 'UNKNOWN');
            $message = (string) ($attributes->Message ?? 'Erreur inconnue');

            throw new Exception("Erreur API MR (Code {$code}): {$message}");
        }

        // Récupérer les données de l'étiquette
        if (! isset($xml->LabelList) || ! isset($xml->LabelList->Label)) {
            throw new Exception('Pas de LabelList dans la réponse API');
        }

        $label = $xml->LabelList->Label;

        $trackingNumber = (string) ($label->ShippingNumber ?? '');
        $labelUrl = (string) ($label->Output ?? '');

        if (empty($trackingNumber) || empty($labelUrl)) {
            throw new Exception('Données étiquette incomplètes dans la réponse');
        }

        return [
            'tracking_number' => $trackingNumber,
            'label_url'       => $labelUrl,
        ];
    }
}
