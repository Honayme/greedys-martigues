<?php

return [
    'mondialrelay' => [
        'code'         => 'mondialrelay',
        'title'        => 'Mondial Relay',
        'description'  => 'Livraison via Mondial Relay (Point Relais, Locker, Domicile)',
        'class'        => 'Webkul\MondialRelay\Carriers\MondialRelay',
        'active'       => true,
        'default_rate' => '3.49',
        'type'         => 'per_order',

        // Tarifs pro 0-9 colis/mois (HT)
        'pricing' => [
            'locker' => 2.99,

            'point_relais' => [
                0    => 3.49,  // 0-250g
                250  => 3.58,  // 250-500g
                500  => 4.49,  // 500-1000g
                1000 => 4.49,  // > 1kg (pour sécurité, à ajuster si nécessaire)
            ],

            'domicile' => [
                0    => 5.00,
                250  => 5.50,
                500  => 6.00,
                1000 => 6.50,
            ],
        ],

        // Codes services Mondial Relay
        'services' => [
            'point_relais' => '24R',  // Point Relais standard
            'locker'       => '24R',  // Lockers utilisent aussi 24R (même code que Point Relais)
            'domicile'     => 'HOM',  // Livraison domicile
        ],
    ],
];
