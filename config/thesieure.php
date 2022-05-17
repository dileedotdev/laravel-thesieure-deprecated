<?php

// config for dinhdjj/laravel-thesieure
return [
    'domain' => env('THESIEURE_DOMAIN', 'thesieure.com'),
    'partner_id' => env('THESIEURE_PARTNER_ID'),
    'partner_key' => env('THESIEURE_PARTNER_KEY'),

    'routes' => [
        'callback' => [
            'name' => 'thesieure.callback',
            'uri' => 'api/thesieure/callback',
            'middleware' => [
                'api',
            ],
            'method' => 'post',
        ],
    ],
];
