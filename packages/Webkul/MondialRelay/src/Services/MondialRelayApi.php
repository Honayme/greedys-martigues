<?php

namespace Webkul\MondialRelay\Services;

use Exception;
use SoapClient;
use SoapFault;

class MondialRelayApi
{
    private ?SoapClient $client = null;

    private string $codeEnseigne;

    private string $privateKey;

    private string $codeMarque;

    public function __construct()
    {
        $this->codeEnseigne = core()->getConfigData('sales.carriers.mondialrelay.code_enseigne') ?? '';
        $this->privateKey = core()->getConfigData('sales.carriers.mondialrelay.private_key') ?? '';
        $this->codeMarque = core()->getConfigData('sales.carriers.mondialrelay.code_marque') ?? '';
    }

    /**
     * Initialise le client SOAP
     */
    private function getClient(): SoapClient
    {
        if ($this->client === null) {
            $apiUrl = core()->getConfigData('sales.carriers.mondialrelay.api_url')
                ?? 'https://api.mondialrelay.com/WebService.asmx';

            try {
                $this->client = new SoapClient($apiUrl.'?WSDL', [
                    'trace'        => 1,
                    'exceptions'   => true,
                    'soap_version' => SOAP_1_1,
                    'cache_wsdl'   => WSDL_CACHE_NONE, // Dev only, à changer en prod
                ]);
            } catch (SoapFault $e) {
                throw new Exception('Erreur connexion SOAP Mondial Relay: '.$e->getMessage());
            }
        }

        return $this->client;
    }

    /**
     * Calcule la signature MD5 selon les specs Mondial Relay
     */
    private function calculateSignature(array $params): string
    {
        $signatureString = implode('', array_values($params)).$this->privateKey;

        return strtoupper(md5($signatureString));
    }

    /**
     * Nettoie une chaîne pour l'API Mondial Relay
     * Supprime les accents, ponctuation et caractères spéciaux
     */
    private function cleanString(string $string): string
    {
        // Supprimer les accents
        $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);

        // Supprimer les caractères spéciaux sauf espaces, lettres et chiffres
        $string = preg_replace('/[^A-Za-z0-9 \-]/', '', $string);

        // Supprimer les espaces multiples
        $string = preg_replace('/\s+/', ' ', $string);

        return trim($string);
    }

    /**
     * Nettoie un numéro de téléphone pour l'API Mondial Relay
     * Garde uniquement les chiffres (supprime espaces, tirets, parenthèses, etc.)
     */
    private function cleanPhone(string $phone): string
    {
        // Supprimer tous les caractères sauf chiffres
        $phone = preg_replace('/[^0-9]/', '', $phone);

        return $phone;
    }

    /**
     * Recherche de points relais
     *
     * @param  string  $postcode  Code postal
     * @param  string  $country  Code pays (FR, BE, etc.)
     * @param  int  $nbResults  Nombre de résultats max
     */
    public function searchPointRelais(string $postcode, string $country = 'FR', int $nbResults = 10): array
    {
        // Vérifier que les credentials sont configurés
        if (empty($this->codeEnseigne) || empty($this->privateKey)) {
            throw new \Exception('Credentials Mondial Relay non configurés dans l\'admin');
        }

        // Tous les paramètres dans l'ordre EXACT du WSDL pour la signature MD5
        // https://api.mondialrelay.com/WebService.asmx?op=WSI3_PointRelais_Recherche
        $paramsForSignature = [
            'Enseigne'       => $this->codeEnseigne,
            'Pays'           => $country,
            'NumPointRelais' => '',
            'Ville'          => '',
            'CP'             => $postcode,
            'Latitude'       => '',
            'Longitude'      => '',
            'Taille'         => '',
            'Poids'          => '',
            'Action'         => '',
            'DelaiEnvoi'     => '',
            'RayonRecherche' => '',
            'TypeActivite'   => '',
            'NACE'           => '',
        ];

        // Calculer la signature
        $signature = $this->calculateSignature($paramsForSignature);

        // Debug temporaire
        \Log::info('MR Search Request', [
            'params'       => $paramsForSignature,
            'signature'    => $signature,
            'codeEnseigne' => $this->codeEnseigne,
        ]);

        // Paramètres à envoyer (avec la signature)
        $params = $paramsForSignature;
        $params['Security'] = $signature;

        try {
            $client = $this->getClient();
            $response = $client->WSI3_PointRelais_Recherche($params);

            // Debug: voir la réponse complète
            \Log::info('MR API Response', [
                'stat'     => $response->WSI3_PointRelais_RechercheResult->STAT ?? 'N/A',
                'response' => json_encode($response),
            ]);

            if (isset($response->WSI3_PointRelais_RechercheResult->STAT)
                && $response->WSI3_PointRelais_RechercheResult->STAT == 0) {

                $points = $response->WSI3_PointRelais_RechercheResult->PointsRelais->PointRelais_Details ?? [];

                // Si un seul résultat, SOAP ne retourne pas un tableau
                if (! is_array($points)) {
                    $points = [$points];
                }

                return $this->formatPointRelais($points);
            }

            throw new Exception('Erreur API MR: '.($response->WSI3_PointRelais_RechercheResult->STAT ?? 'Inconnue'));
        } catch (SoapFault $e) {
            \Log::error('MR SOAP Error', ['error' => $e->getMessage()]);
            throw new Exception('Erreur recherche points relais: '.$e->getMessage());
        }
    }

    /**
     * Formate les points relais pour le frontend
     */
    private function formatPointRelais(array $points): array
    {
        $formatted = [];

        foreach ($points as $point) {
            // Déterminer le type : Locker (24L) ou Point Relais (24R)
            $information = $point->Information ?? '';
            $isLocker = stripos($information, 'LOCKER') !== false
                        || stripos($point->LgAdr1 ?? '', 'LOCKER') !== false;

            $formatted[] = [
                'id'        => $point->Num ?? '',
                'name'      => $point->LgAdr1 ?? '',
                'address'   => trim(($point->LgAdr3 ?? '').' '.($point->LgAdr4 ?? '')),
                'postcode'  => $point->CP ?? '',
                'city'      => $point->Ville ?? '',
                'country'   => $point->Pays ?? '',
                'latitude'  => $point->Latitude ?? null,
                'longitude' => $point->Longitude ?? null,
                'type'      => $isLocker ? '24L' : '24R',
                'horaires'  => $this->parseHoraires($point),
            ];
        }

        return $formatted;
    }

    /**
     * Parse les horaires du point relais
     */
    private function parseHoraires($point): array
    {
        $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
        $horaires = [];

        foreach ($jours as $i => $jour) {
            $key = 'Horaires_'.$jour;
            if (isset($point->$key)) {
                $horaires[$jour] = $point->$key;
            }
        }

        return $horaires;
    }

    /**
     * Création d'une étiquette
     *
     * @param  array  $orderData  Données de la commande
     * @return array ['tracking_number', 'label_url']
     */
    public function createLabel(array $orderData): array
    {
        // Déterminer si livraison en point relais (24R/24L) ou à domicile (HOM)
        $isPointRelais = in_array($orderData['delivery_mode'], ['24R', '24L']);

        // Paramètres 1-45 pour le calcul de la signature MD5 (Texte est EXCLU de la signature)
        $paramsForSignature = [
            'Enseigne'     => $this->codeEnseigne,
            'ModeCol'      => 'CCC', // Collecte par le commerçant
            'ModeLiv'      => $orderData['delivery_mode'], // 24R, 24L, HOM
            'NDossier'     => (string) $orderData['order_id'],
            'NClient'      => (string) ($orderData['customer_id'] ?? ''),
            'Expe_Langage' => 'FR',
            'Expe_Ad1'     => substr($this->cleanString($orderData['sender']['name']), 0, 32),
            'Expe_Ad2'     => '',
            'Expe_Ad3'     => substr($this->cleanString($orderData['sender']['address']), 0, 32),
            'Expe_Ad4'     => '',
            'Expe_Ville'   => substr($this->cleanString($orderData['sender']['city']), 0, 26),
            'Expe_CP'      => $orderData['sender']['postcode'],
            'Expe_Pays'    => $orderData['sender']['country'],
            'Expe_Tel1'    => $this->cleanPhone($orderData['sender']['phone']),
            'Expe_Tel2'    => '',
            'Expe_Mail'    => $orderData['sender']['email'],
            'Dest_Langage' => 'FR',
            'Dest_Ad1'     => substr($this->cleanString($orderData['recipient']['name']), 0, 32),
            'Dest_Ad2'     => '',
            'Dest_Ad3'     => substr($this->cleanString($orderData['recipient']['address']), 0, 32),
            'Dest_Ad4'     => '',
            'Dest_Ville'   => substr($this->cleanString($orderData['recipient']['city']), 0, 26),
            'Dest_CP'      => $orderData['recipient']['postcode'],
            'Dest_Pays'    => $orderData['recipient']['country'],
            'Dest_Tel1'    => $this->cleanPhone($orderData['recipient']['phone']),
            'Dest_Tel2'    => '',
            'Dest_Mail'    => $orderData['recipient']['email'],
            'Poids'        => (string) ($orderData['weight'] * 1000), // En grammes
            'Longueur'     => '0',
            'Taille'       => 'XS', // XS obligatoire pour les petits colis
            'NbColis'      => '1',
            'CRT_Valeur'   => '0', // Pas de contre-remboursement (commande déjà payée)
            'CRT_Devise'   => 'EUR',
            'Exp_Valeur'   => '',
            'Exp_Devise'   => '',
            'COL_Rel_Pays' => '',
            'COL_Rel'      => '',
            'LIV_Rel_Pays' => $isPointRelais ? ($orderData['recipient']['country'] ?? 'FR') : '',
            'LIV_Rel'      => $isPointRelais ? ($orderData['point_relais_id'] ?? '') : '',
            'TAvisage'     => '',
            'TReprise'     => '',
            'Montage'      => '0',
            'TRDV'         => '',
            'Assurance'    => '0',
            'Instructions' => '',
        ];

        // Calculer la signature MD5 avec les paramètres 1-45 (sans Texte)
        $signature = $this->calculateSignature($paramsForSignature);

        // Construire le tableau final dans l'ordre API : paramètres 1-45, Security (#46), Texte (#47)
        $params = $paramsForSignature;
        $params['Security'] = $signature;
        $params['Texte'] = '';

        // Debug: logger les paramètres pour vérification de la signature
        $signatureString = implode('', array_values($paramsForSignature)).$this->privateKey;
        \Log::info('MR Signature Debug', [
            'params_for_signature' => $paramsForSignature,
            'concatenated_string'  => $signatureString,
            'calculated_signature' => strtoupper(md5($signatureString)),
            'private_key_used'     => $this->privateKey,
        ]);

        // Debug: logger les paramètres envoyés
        \Log::info('MR CreateLabel Request', [
            'params'    => $params,
            'signature' => $params['Security'],
        ]);

        try {
            $client = $this->getClient();
            $response = $client->WSI2_CreationEtiquette($params);

            // Logger la requête SOAP brute pour debug
            \Log::info('MR SOAP Request XML', [
                'request' => $client->__getLastRequest(),
            ]);

            \Log::info('MR CreateLabel Response', [
                'stat'     => $response->WSI2_CreationEtiquetteResult->STAT ?? 'N/A',
                'response' => json_encode($response),
            ]);

            if (isset($response->WSI2_CreationEtiquetteResult->STAT)
                && $response->WSI2_CreationEtiquetteResult->STAT == 0) {

                $labelUrl = $response->WSI2_CreationEtiquetteResult->URL_Etiquette ?? '';

                // Si l'URL est relative, préfixer avec le domaine Mondial Relay
                if (! empty($labelUrl) && str_starts_with($labelUrl, '/')) {
                    $labelUrl = 'https://www.mondialrelay.com'.$labelUrl;
                }

                return [
                    'tracking_number' => $response->WSI2_CreationEtiquetteResult->ExpeditionNum ?? '',
                    'label_url'       => $labelUrl,
                ];
            }

            throw new Exception('Erreur création étiquette: '.($response->WSI2_CreationEtiquetteResult->STAT ?? 'Inconnue'));
        } catch (SoapFault $e) {
            throw new Exception('Erreur SOAP création étiquette: '.$e->getMessage());
        }
    }
}
