<?php

// config for dinhdjj/laravel-thesieure
return [
    'domain' => env('THESIEURE_DOMAIN', 'thesieure.com'),
    'partner_id' => env('THESIEURE_PARTNER_ID'),
    'partner_key' => env('THESIEURE_PARTNER_KEY'),

    /**
     * The callback will be call when thesieure callback to server.
     */
    'callback' => [
        'route' => [
            'name' => 'thesieure.callback',
            'uri' => 'api/thesieure/callback',
            'middleware' => [
                'api',
            ],
            'method' => 'post',
        ],
    ],

    /**
     * Used when fetch card types from thesieure server.
     */
    'fetch_card_types' => [
        'cache' => [
            'enabled' => true,
            'key' => 'thesieure.card_types',
            'ttl' => 60 * 5, // 5 minutes,
            'store' => null, // used default store
        ],
    ],
];
