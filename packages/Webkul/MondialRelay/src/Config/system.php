<?php

return [
    [
        'key'  => 'sales.carriers.mondialrelay',
        'name' => 'Mondial Relay',
        'info' => 'Configure Mondial Relay shipping (Point Relais, Locker, Domicile)',
        'sort' => 3,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'Title',
                'type'          => 'text',
                'default'       => 'Mondial Relay',
                'depends'       => 'active:1',
                'validation'    => 'required_if:active,1',
                'channel_based' => true,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'Description',
                'type'          => 'textarea',
                'channel_based' => true,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'Status',
                'type'          => 'boolean',
                'default'       => false,
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'       => 'code_enseigne',
                'title'      => 'Code Enseigne',
                'type'       => 'text',
                'depends'    => 'active:1',
                'validation' => 'required_if:active,1',
                'info'       => 'Fourni par Mondial Relay',
            ], [
                'name'       => 'private_key',
                'title'      => 'Clé Privée',
                'type'       => 'password',
                'depends'    => 'active:1',
                'validation' => 'required_if:active,1',
                'info'       => 'Clé privée pour signature MD5',
            ], [
                'name'       => 'code_marque',
                'title'      => 'Code Marque',
                'type'       => 'text',
                'depends'    => 'active:1',
                'validation' => 'required_if:active,1',
            ], [
                'name'    => 'api_url',
                'title'   => 'URL API',
                'type'    => 'text',
                'default' => 'https://api.mondialrelay.com/WebService.asmx',
            ], [
                'name'    => 'enable_point_relais',
                'title'   => 'Activer Points Relais',
                'type'    => 'boolean',
                'default' => true,
            ], [
                'name'    => 'enable_locker',
                'title'   => 'Activer Lockers',
                'type'    => 'boolean',
                'default' => true,
            ], [
                'name'    => 'enable_domicile',
                'title'   => 'Activer Livraison Domicile',
                'type'    => 'boolean',
                'default' => false,
                'info'    => 'Nécessite les tarifs LD1',
            ],
        ],
    ],
];
