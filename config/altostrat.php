<?php


// Function to parse comma-separated env strings into an array
if (!function_exists('parseEnvList')) {
    function parseEnvList(?string $stringList, array $default = []): array
    {
        if (is_null($stringList)) {
            return $default;
        }
        $list = array_filter(array_map('trim', explode(',', $stringList)));
        return empty($list) ? $default : $list;
    }
}

return [

        'key' => env('ALTOSTRAT_KEY', 'altostrat-key'),
        'url' => env('ALTOSTRAT_URL', 'https://api.altostrat.io/'),
        'logging' => [
                'endpoint' => env('ALTOSTRAT_LOGGING_API', 'log-eater/log'),
                'disabled' => env('ALTOSTRAT_LOGGING_DISABLED', false),
        ],


        'api_prefix' => env('API_PREFIX', 'api'),
        'amplitude' => [
                'api_key' => env('AMPLITUDE_API_KEY'),
                'endpoint' => env('AMPLITUDE_ENDPOINT', 'https://api2.amplitude.com/2/httpapi'),
        ],

        'customer_model' => env('ALTOSTRAT_CUSTOMER_MODEL', \App\Models\Customer::class),

        'oidc' => [
                'providers' => [

                        'workos' => [
                                'issuer' => env('WORKOS_OIDC_ISSUER'),

                                'well_known_url' => env('WORKOS_OIDC_WELL_KNOWN_URL', env('WORKOS_OIDC_ISSUER')),
                                'audience' => env('WORKOS_OIDC_AUDIENCE'),
                                'staging_audience' => env('WORKOS_OIDC_STAGING_AUDIENCE'),

                        ],

                        'passport' => [
                                'issuer' => env('PASSPORT_OIDC_ISSUER'),
                                'well_known_url' => env('PASSPORT_OIDC_WELL_KNOWN_URL', env('PASSPORT_OIDC_ISSUER')),
                                'audience' => env('PASSPORT_OIDC_AUDIENCE'),
                                'staging_audience' => env('PASSPORT_OIDC_AUDIENCE'),
                        ],
                ],

                'allowed_algorithms' => parseEnvList(env('OIDC_ALLOWED_ALGORITHMS'), ['RS256']),
                'jwks_cache_ttl' => (int) env('OIDC_JWKS_CACHE_TTL', 3600),
        ]

];
