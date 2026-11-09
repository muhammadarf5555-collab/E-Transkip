<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

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

    'siakad' => [
        'base_url' => rtrim(env('SIAKAD_API_BASE_URL', 'http://localhost:8000'), '/'),
        'timeout' => (int) env('SIAKAD_API_TIMEOUT', 10),
    ],

    'bridge' => [
        'base_url' => rtrim(env('BRIDGE_API_BASE_URL', 'https://bridge.uinssc.ac.id/api'), '/'),
        'token' => env('BRIDGE_API_TOKEN', ''),
        'timeout' => (int) env('BRIDGE_API_TIMEOUT', 10),
    ],

    'ewisuda' => [
        'base_url' => rtrim(env('EWISUDA_API_BASE_URL', 'https://wisuda.uinssc.ac.id'), '/'),
        'api_key' => env('EWISUDA_API_KEY', ''),
        'timeout' => (int) env('EWISUDA_API_TIMEOUT', 15),
    ],

];
