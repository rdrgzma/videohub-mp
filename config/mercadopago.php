<?php

return [
    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'client_id' => env('MERCADOPAGO_CLIENT_ID'),
    'client_secret' => env('MERCADOPAGO_CLIENT_SECRET'),
    'environment' => env('MERCADOPAGO_ENVIRONMENT', 'sandbox'),
    'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),

    // URLs de retorno
    'success_url' => env('APP_URL') . '/subscription/success',
    'failure_url' => env('APP_URL') . '/subscription/failure',
    'pending_url' => env('APP_URL') . '/subscription/pending',

    // Configurações específicas
    'statement_descriptor' => 'VIDEOHUB',
    'notification_url' => env('APP_URL') . '/webhooks/mercadopago',
];
