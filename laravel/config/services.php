<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'mailgun'  => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Flask AI Engine
    |--------------------------------------------------------------------------
    */

    'flask' => [
        'url'     => env('FLASK_API_URL', 'http://localhost:5000'),
        'api_key' => env('FLASK_API_KEY', 'supersecret-internal-api-key-change-this'),
        'timeout' => (int) env('FLASK_TIMEOUT', 15),
    ],

];
