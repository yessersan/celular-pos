<?php

return [
    'postmark' => ['key' => env('POSTMARK_API_KEY')],
    'resend' => ['key' => env('RESEND_API_KEY')],
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'apisperu' => [
        'base_url' => env('APISPERU_DNIRUC_URL', 'https://dniruc.apisperu.com/api/v1'),
        'token' => env('APISPERU_TOKEN'),
        'billing_url' => env('APISPERU_BILLING_URL', 'https://facturacion.apisperu.com/api/v1'),
        'billing_token' => env('APISPERU_BILLING_TOKEN'),
        'company_ruc' => env('APISPERU_COMPANY_RUC'),
        'company_name' => env('APISPERU_COMPANY_NAME'),
        'company_address' => env('APISPERU_COMPANY_ADDRESS'),
    ],
];
