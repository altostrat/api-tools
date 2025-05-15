<?php

return [

    'key' => env('ALTOSTRAT_KEY', 'altostrat-key'),
    'url' => env('ALTOSTRAT_URL', 'https://api.altostrat.io/'),
    'logging' => [
        'endpoint' => env('ALTOSTRAT_LOGGING_API', 'log-eater/log'),
        'disabled' => env('ALTOSTRAT_LOGGING_DISABLED', false),
    ],
    'auth0' => [
        'legacy' => [
            'client_id' => env('AUTH0_LEGACY_CLIENT_ID'),
            'domain' => env('AUTH0_LEGACY_DOMAIN', 'auth.altostrat.app'),
            'cookie_secret' => env('AUTH0_COOKIE_SECRET'),
        ],
        'client_id' => env('AUTH0_CLIENT_ID'),
        'domain' => env('AUTH0_DOMAIN', 'auth.altostrat.io'),
        'cookie_secret' => env('AUTH0_COOKIE_SECRET'),
    ],
    'customer_model' => env('ALTOSTRAT_CUSTOMER_MODEL', 'App\Models\Customer'),
    'api_prefix' => env('API_PREFIX', 'api'),
    'amplitude' => [
        'api_key' => env('AMPLITUDE_API_KEY'),
        'endpoint' => env('AMPLITUDE_ENDPOINT', 'https://api2.amplitude.com/2/httpapi'),
    ],
];
