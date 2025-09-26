<?php

return [
    'gateways' => [
        'credit_card' => [
            'api_key' => env('CREDIT_CARD_API_KEY'),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
        ],
    ],
];
