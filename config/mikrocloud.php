<?php

return [

    'key' => env('MIKROCLOUD_KEY', 'mikrocloud-key'),
    'url' => env('MIKROCLOUD_URL', 'https://api.mikrocloud.com/'),
    'logging' => [
        'endpoint' => env('MIKROCLOUD_LOGGING_API', 'log-eater/log'),
        'disabled' => env('MIKROCLOUD_LOGGING_DISABLED', false),
    ],
    'auth0' => [
        'client_id' => env('AUTH0_CLIENT_ID'),
        'domain' => env('AUTH0_DOMAIN', 'auth.mikrocloud.com'),
        'cookie_secret' => env('AUTH0_COOKIE_SECRET'),
    ],
    'customer_model' => env('MIKROCLOUD_CUSTOMER_MODEL', 'App\Models\Customer'),
    'api_prefix' => env('API_PREFIX', 'api'),
    'amplitude' => [
        'api_key' => env('AMPLITUDE_API_KEY'),
        'endpoint' => env('AMPLITUDE_ENDPOINT', 'https://api2.amplitude.com/2/httpapi'),
    ],
];
