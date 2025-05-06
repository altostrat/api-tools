<?php

namespace Altostrat\Tools\Http\Middleware;

use Altostrat\Tools\Jobs\AuditLogJob;
use Closure;
use Firebase\JWT\BeforeValidException;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class Auth0Users
{
    /**
     * Get all validated OIDC provider configurations, adjusting for environment.
     *
     * @return array{providers: array<string, array>, shared: array}
     * @throws \RuntimeException
     */
    protected function getOidcConfigs(): array
    {
        $providersConfig = config('altostrat.oidc.providers');
        if (empty($providersConfig) || !is_array($providersConfig)) {
            throw new \RuntimeException('OIDC provider configurations (altostrat.oidc.providers) are missing or invalid.');
        }

        $validatedProviders = [];
        $env = config('app.env');

        foreach ($providersConfig as $key => $provider) {
            // Use null coalescing for safety
            $issuer = $provider['issuer'] ?? null;
            $audience = $provider['audience'] ?? null;
            $stagingAudience = $provider['staging_audience'] ?? null;
            // Default well_known_url to issuer if it's not set or is null/empty
            $wellKnownUrl = (!empty($provider['well_known_url'])) ? $provider['well_known_url'] : $issuer;

            if (empty($issuer) || empty($audience) || empty($wellKnownUrl)) {
                Log::warning("OIDC config incomplete for provider key '{$key}'. Requires issuer, audience, and well_known_url (or issuer). Skipping.");
                continue;
            }

            // Adjust audience based on environment if a specific staging audience is provided
            if ($env === 'staging' && !empty($stagingAudience)) {
                $audience = $stagingAudience;
            }

            // Store the final config for this provider
            $validatedProviders[$key] = [
                    'issuer' => rtrim($issuer, '/'),
                    'audience' => $audience, // Already adjusted for env
                    'well_known_url' => rtrim($wellKnownUrl, '/'),
            ];
        }

        if (empty($validatedProviders)) {
            throw new \RuntimeException('No valid OIDC provider configurations found after validation.');
        }

        return [
                'providers' => $validatedProviders,
                'shared' => [
                    // Algorithms aren't directly used in this flow but good to have
                        'allowed_algorithms' => config('altostrat.oidc.allowed_algorithms', ['RS256']),
                        'jwks_cache_ttl' => config('altostrat.oidc.jwks_cache_ttl', 3600),
                ]
        ];
    }

    /**
     * Fetch JWKS data from the OIDC provider, using cache for the raw data.
     * (Method remains the same as previous version)
     *
     * @param  string  $wellKnownUrl  The URL base for discovery or direct JWKS URI if known.
     * @param  int  $cacheTtl
     * @return array The raw JWKS data array fetched from the endpoint.
     * @throws \RuntimeException If data cannot be fetched or is invalid.
     */
    protected function fetchJwksData(string $wellKnownUrl, int $cacheTtl): array
    {
        $cacheKey = 'jwks_raw_data_'.md5($wellKnownUrl);

        return Cache::remember($cacheKey, $cacheTtl, function () use ($wellKnownUrl) {
            Log::debug("JWKS cache miss for source: {$wellKnownUrl}. Fetching fresh data.");
            $jwksUri = null;
            try {
                // Assume $wellKnownUrl is the base for OIDC discovery
                $discoveryUrl = $wellKnownUrl.'/.well-known/openid-configuration';
                Log::debug("Attempting OIDC discovery at: {$discoveryUrl}");
                $discoveryResponse = Http::timeout(5)->get($discoveryUrl);

                if ($discoveryResponse->successful()) {
                    $jwksUri = $discoveryResponse->json('jwks_uri');
                    if (!$jwksUri) {
                        Log::error("jwks_uri not found in OIDC discovery document from {$discoveryUrl}");
                        throw new \RuntimeException("jwks_uri not found in OIDC discovery document from {$discoveryUrl}");
                    }
                    Log::debug("Found jwks_uri via discovery: {$jwksUri}");
                } else {
                    // Fallback: Maybe the wellKnownUrl *is* the JWKS URI? (Less common for OIDC)
                    // Or handle error strictly. Let's be strict for now.
                    Log::error("Failed to fetch OIDC discovery document from {$discoveryUrl}: Status ".$discoveryResponse->status());
                    throw new \RuntimeException("Failed to fetch OIDC discovery document from {$discoveryUrl}.");
                }

                // Fetch JWKS Keys using the obtained URI
                Log::debug("Fetching JWKS from: {$jwksUri}");
                $jwksResponse = Http::timeout(5)->get($jwksUri);
                if (!$jwksResponse->successful()) {
                    Log::error("Failed to fetch JWKS from {$jwksUri}: Status ".$jwksResponse->status());
                    throw new \RuntimeException("Failed to fetch JWKS from {$jwksUri}.");
                }

                $jwks = $jwksResponse->json();
                if (!is_array($jwks) || !isset($jwks['keys']) || !is_array($jwks['keys'])) {
                    Log::error("Invalid JWKS format fetched from {$jwksUri}: Missing or invalid 'keys' array.",
                            ['jwks_response_body_preview' => substr($jwksResponse->body(), 0, 500)]);
                    throw new \RuntimeException("Invalid JWKS format received from {$jwksUri}.");
                }

                Log::info("Successfully fetched raw JWKS from {$jwksUri} (via {$wellKnownUrl}). Caching raw data.");
                return $jwks;

            } catch (Throwable $e) {
                Log::error("Exception while fetching raw JWKS originating from {$wellKnownUrl}: ".$e->getMessage(),
                        ['exception' => $e]);
                throw new \RuntimeException("Could not retrieve valid raw JWKS data originating from {$wellKnownUrl}.",
                        0, $e);
            }
        });
    }

    /**
     * Attempt to validate the token against a specific provider configuration.
     * Includes temporary workaround for tokens missing 'kid' IF JWKS has only one key.
     *
     * @param  string  $bearerToken
     * @param  array  $providerConfig  (contains 'issuer', 'audience', 'well_known_url')
     * @param  array  $sharedConfig  (contains 'jwks_cache_ttl')
     * @return array|null Decoded claims array on success, null on expected mismatch (iss/aud)
     * @throws ExpiredException|SignatureInvalidException|BeforeValidException|\UnexpectedValueException For fatal validation errors for this token.
     * @throws \RuntimeException For JWKS fetch/parse errors specific to this provider attempt.
     * @throws Throwable For other unexpected errors.
     */
    protected function attemptTokenValidation(string $bearerToken, array $providerConfig, array $sharedConfig): ?array
    {
        $currentIssuer = $providerConfig['issuer']; // For logging context
        Log::debug("Attempting validation with: Issuer={$currentIssuer}, Audience={$providerConfig['audience']}, WellKnownURL={$providerConfig['well_known_url']}");

        // 1. Fetch RAW JWKS data
        $jwksRawData = $this->fetchJwksData($providerConfig['well_known_url'], $sharedConfig['jwks_cache_ttl']);

        // 2. Parse JWKS data
        $jwksKeySet = JWK::parseKeySet($jwksRawData);
        $keyCount = count($jwksKeySet);
        Log::debug("Parsed JWKS keyset for issuer {$currentIssuer}.", [
                'kids_found' => array_keys($jwksKeySet),
                'key_count' => $keyCount,
                'config_used' => $providerConfig
        ]);

        if (empty($jwksKeySet)) {
            Log::warning("No valid signing keys parsed from JWKS for provider config.", ['config' => $providerConfig]);
            throw new \RuntimeException("No valid signing keys found for issuer {$currentIssuer}.");
        }

        // ---=== TEMPORARY WORKAROUND for Missing 'kid' (Passport) ===---
        // This assumes the relevant JWKS endpoint contains exactly ONE signing key.
        $keyMaterialToUse = $jwksKeySet; // Default to the full set for standard kid lookup

        // Check if workaround conditions are met: Only one key parsed AND this is the Passport provider attempt
        // (We only apply this logic specifically for the known 'bad' provider)
        // Ensure the key 'passport' matches your config key.
        $isPassportProvider = ($currentIssuer === config('altostrat.oidc.providers.passport.issuer'));

        if ($isPassportProvider && $keyCount === 1) {
            Log::debug("Single key found in JWKS for Passport provider. Checking token header for 'kid'.");
            try {
                // Decode header only - Check if 'kid' is actually missing from this token
                $tokenParts = explode('.', $bearerToken);
                if (count($tokenParts) === 3) { // Basic check for JWT structure
                    $encodedHeader = $tokenParts[0];
                    $header = JWT::jsonDecode(JWT::urlsafeB64Decode($encodedHeader));
                    if (!isset($header->kid) || empty($header->kid)) {
                        Log::warning("WORKAROUND APPLIED: Token header missing 'kid' for Passport issuer. Using the single key from JWKS.",
                                ['issuer' => $currentIssuer]);
                        // Use the single key directly, bypassing standard kid lookup
                        $keyMaterialToUse = reset($jwksKeySet); // Get the first (and only) key's value
                    } else {
                        Log::debug("Token header contains 'kid'. Workaround not needed, proceeding with standard keyset lookup.",
                                ['issuer' => $currentIssuer, 'kid' => $header->kid]);
                        // $keyMaterialToUse remains $jwksKeySet
                    }
                } else {
                    Log::warning("Could not split token into 3 parts to check header 'kid'. Proceeding with standard keyset lookup.",
                            ['issuer' => $currentIssuer]);
                }
            } catch (Throwable $th) {
                Log::error("Failed to decode token header while checking for 'kid'. Proceeding with standard keyset lookup.",
                        ['issuer' => $currentIssuer, 'error' => $th->getMessage()]);
                // Fallback to standard keyset lookup if header decode fails
                // $keyMaterialToUse remains $jwksKeySet
            }
        } elseif ($isPassportProvider) {
            Log::debug("Workaround conditions not met for Passport (Key count: {$keyCount} != 1). Standard 'kid' lookup required.",
                    ['issuer' => $currentIssuer]);
        }
        // ---=== END TEMPORARY WORKAROUND ===---


        // 3. Decode the token using the determined key material
        // $keyMaterialToUse is either the full $jwksKeySet array or the single key material if workaround applied
        Log::debug("Attempting JWT::decode.", ['using_single_key_material' => !is_array($keyMaterialToUse)]);
        $decodedPayload = JWT::decode($bearerToken, $keyMaterialToUse);
        Log::debug("JWT::decode successful.");


        // 4. STRICTLY validate issuer
        if (!isset($decodedPayload->iss) || $decodedPayload->iss !== $providerConfig['issuer']) {
            Log::debug("Token issuer mismatch.",
                    ['expected' => $providerConfig['issuer'], 'actual' => $decodedPayload->iss ?? 'N/A']);
            return null;
        }

        // 5. STRICTLY validate audience
        $tokenAudience = isset($decodedPayload->aud) ? (array) $decodedPayload->aud : [];
        if (!in_array($providerConfig['audience'], $tokenAudience)) {
            Log::debug("Token audience mismatch.",
                    ['expected' => $providerConfig['audience'], 'actual' => $tokenAudience]);
            return null;
        }

        // If all checks pass...
        Log::info("Token validation successful for issuer: {$providerConfig['issuer']}");
        return (array) $decodedPayload;
    }

    /**
     * INSECURELY decode token payload to extract audience claim(s) BEFORE verification.
     * Use with caution and only for routing logic before proper validation.
     *
     * @param  string  $token  The JWT string.
     * @return string|array|null The audience claim value(s) or null on failure.
     */
    protected function getUnverifiedAudienceFromToken(string $token): string|array|null
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                Log::warning('Token does not have 3 parts, cannot extract audience.');
                return null;
            }
            // Note: No signature verification here!
            $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($parts[1]));
            // 'aud' can be string or array
            return $payload->aud ?? null;
        } catch (Throwable $e) {
            Log::warning('Failed to decode token payload (before verification) to extract audience.', [
                    'error' => $e->getMessage(),
                    'token_preview' => substr($token, 0, 20).'...'
            ]);
            return null;
        }
    }

    /**
     * Check if a given string matches the UUID format.
     * Using a common UUID regex (adjust if needed).
     * @param  string  $value
     * @return bool
     */
    protected function isUuid(string $value): bool
    {
        return preg_match('/^[a-f\d]{8}-[a-f\d]{4}-[1-5][a-f\d]{3}-[89ab][a-f\d]{3}-[a-f\d]{12}$/i', $value) === 1;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $bearerToken = $request->bearerToken();
        if (!$bearerToken) {
            return response()->json(['message' => 'Bearer token not provided.'], 401);
        }

        $claims = null;
        $selectedProviderConfig = null;
        $selectedProviderKey = null; // 'passport' or 'workos'

        try {
            // 1. Get all possible OIDC configurations
            $oidcConfigs = $this->getOidcConfigs();
            $providers = $oidcConfigs['providers'];
            $sharedConfig = $oidcConfigs['shared'];

            // 2. *** INSECURE ***: Extract audience from token payload BEFORE verification
            $unverifiedAudience = $this->getUnverifiedAudienceFromToken($bearerToken);
            Log::debug('Extracted unverified audience from token.', ['unverified_audience' => $unverifiedAudience]);

            if ($unverifiedAudience === null) {
                throw new \UnexpectedValueException('Could not extract audience claim from token to determine provider.');
            }

            // 3. Select provider based on whether *any* audience value is a UUID
            $audienceContainsUuid = false;
            if (is_array($unverifiedAudience)) {
                foreach ($unverifiedAudience as $aud) {
                    if (is_string($aud) && $this->isUuid($aud)) {
                        $audienceContainsUuid = true;
                        break;
                    }
                }
            } elseif (is_string($unverifiedAudience) && $this->isUuid($unverifiedAudience)) {
                $audienceContainsUuid = true;
            }

            // Determine provider key based on audience format
            $selectedProviderKey = $audienceContainsUuid ? 'passport' : 'workos';
            Log::info("Selected provider configuration '{$selectedProviderKey}' based on audience format (UUID found: ".($audienceContainsUuid ? 'yes' : 'no').").");


            // Check if the selected provider configuration actually exists
            if (!isset($providers[$selectedProviderKey])) {
                Log::error("Selected provider configuration key '{$selectedProviderKey}' does not exist in settings.",
                        ['available_keys' => array_keys($providers)]);
                // This indicates a fundamental config issue
                throw new \RuntimeException("Configuration error: Provider '{$selectedProviderKey}' not found.");
            }
            $selectedProviderConfig = $providers[$selectedProviderKey];


            // --- Now attempt validation using the *selected* provider ---

            Log::debug("Attempting validation using selected provider: '{$selectedProviderKey}'",
                    ['config' => $selectedProviderConfig]);

            // 4. Fetch JWKS data for the selected provider
            $jwksRawData = $this->fetchJwksData($selectedProviderConfig['well_known_url'],
                    $sharedConfig['jwks_cache_ttl']);

            // 5. Parse JWKS data
            $jwksKeySet = JWK::parseKeySet($jwksRawData);
            $keyCount = count($jwksKeySet);
            Log::debug("Parsed JWKS keyset for selected provider '{$selectedProviderKey}'.", [
                    'kids_found' => array_keys($jwksKeySet), 'key_count' => $keyCount
            ]);
            if (empty($jwksKeySet)) {
                throw new \RuntimeException("No valid signing keys found in JWKS for issuer {$selectedProviderConfig['issuer']}.");
            }

            // 6. Determine key material (apply workaround ONLY if Passport is selected)
            $keyMaterialToUse = $jwksKeySet; // Default to full set
            if ($selectedProviderKey === 'passport' && $keyCount === 1) {
                Log::debug("Provider is 'passport' and single key found. Checking token header 'kid' for workaround.");
                try {
                    $tokenParts = explode('.', $bearerToken);
                    if (count($tokenParts) === 3) {
                        $header = JWT::jsonDecode(JWT::urlsafeB64Decode($tokenParts[0]));
                        if (!isset($header->kid) || empty($header->kid)) {
                            Log::warning("WORKAROUND APPLIED: Token missing 'kid', using single key from Passport JWKS.",
                                    ['issuer' => $selectedProviderConfig['issuer']]);
                            $keyMaterialToUse = reset($jwksKeySet); // Use the single key's value
                        } else {
                            Log::debug("Token header has 'kid'. Workaround not needed.", ['kid' => $header->kid]);
                        }
                    } else {
                        Log::warning("Cannot check token header 'kid' (token parts != 3). Using full keyset.",
                                ['issuer' => $selectedProviderConfig['issuer']]);
                    }
                } catch (Throwable $th) {
                    Log::error("Failed to decode token header while checking for 'kid'. Using full keyset.",
                            ['error' => $th->getMessage()]);
                }
            }

            // 7. Decode & Verify Token (Signature, Expiry, NBF, etc.)
            Log::debug("Attempting JWT::decode using determined key material.",
                    ['using_single_key' => !is_array($keyMaterialToUse)]);
            $decodedPayload = JWT::decode($bearerToken, $keyMaterialToUse);
            Log::debug("JWT::decode successful (signature/time validated).");

            // 8. Re-validate Issuer & Audience (defense-in-depth after signature check)
            // Ensure the issuer in the now-verified token matches the one expected for the provider we selected
            if (!isset($decodedPayload->iss) || rtrim($decodedPayload->iss,
                            '/') !== $selectedProviderConfig['issuer']) {
                Log::error("CRITICAL: Token issuer claim does not match expected issuer for the provider selected based on audience!",
                        [
                                'selected_provider' => $selectedProviderKey,
                                'expected_issuer' => $selectedProviderConfig['issuer'],
                                'actual_issuer' => $decodedPayload->iss ?? 'N/A'
                        ]);
                // This could indicate token manipulation if the unverified aud didn't align with the verified iss
                throw new \UnexpectedValueException("Token issuer validation failed after decoding.");
            }
            // Ensure the audience in the now-verified token matches the one expected for the selected provider
            $verifiedTokenAudience = isset($decodedPayload->aud) ? (array) $decodedPayload->aud : [];
            if (!in_array($selectedProviderConfig['audience'], $verifiedTokenAudience)) {
                Log::warning("Token audience mismatch after decoding.", [
                        'selected_provider' => $selectedProviderKey,
                        'expected_audience' => $selectedProviderConfig['audience'],
                        'actual_audience' => $verifiedTokenAudience
                ]);
                throw new \UnexpectedValueException("Invalid token audience. Expected {$selectedProviderConfig['audience']}.");
            }

            // If all checks pass...
            $claims = (array) $decodedPayload;
            Log::info("Token validation successful.", [
                    'provider' => $selectedProviderKey, 'issuer' => $selectedProviderConfig['issuer'],
                    'audience' => $selectedProviderConfig['audience']
            ]);


            // --- Exception Handling ---
        } catch (ExpiredException $e) { // Keep specific catches first
            Log::warning("JWT validation failed: Token has expired.", ['exception_message' => $e->getMessage()]);
            return response()->json(['message' => 'Token has expired.'], 401);
        } catch (SignatureInvalidException $e) {
            Log::warning("JWT validation failed: Token signature verification failed.",
                    ['exception_message' => $e->getMessage()]);
            return response()->json(['message' => 'Token signature verification failed.'], 401);
        } catch (BeforeValidException $e) {
            Log::warning("JWT validation failed: Token cannot be used yet (before nbf time).",
                    ['exception_message' => $e->getMessage()]);
            return response()->json(['message' => 'Token cannot be used yet (before nbf time).'], 401);
        } catch (\UnexpectedValueException $e) { // Catches format issues, explicit iss/aud fails, kid errors if workaround fails, selection failures
            Log::warning('JWT Validation Error: '.$e->getMessage(), ['exception' => $e]);
            // Provide slightly more specific feedback based on common errors
            if (str_contains($e->getMessage(), 'audience')) {
                return response()->json(['message' => 'Invalid token audience.'], 401);
            } elseif (str_contains($e->getMessage(), 'issuer')) {
                return response()->json(['message' => 'Invalid token issuer.'], 401);
            } elseif (str_contains($e->getMessage(), '"kid"')) {
                return response()->json(['message' => 'Invalid token signature key identifier.'], 401);
            } elseif (str_contains($e->getMessage(), 'extract audience claim')) {
                return response()->json(['message' => 'Invalid token structure.'], 401);
            }
            return response()->json(['message' => 'Invalid token.'], 401); // Generic fallback
        } catch (\RuntimeException $e) { // Catch config/JWKS errors
            Log::error('Middleware Configuration/Runtime Error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Server error retrieving validation keys or configuration.'], 500);
        } catch (Throwable $e) { // Catch other unexpected issues
            Log::error('JWT Middleware Error: '.$e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Could not process token due to an internal server error.'], 500);
        }


        // --- Post-Validation Logic (using $claims) ---
        // This section remains unchanged
        $customer_id = Arr::get($claims, 'id');
        abort_unless($customer_id, 401, 'Customer ID not found in token');

        app()->bind('App\Models\Customer', function () use ($customer_id) {
            $customer = new \App\Models\Customer(); // Adapt instantiation if needed
            $customer->id = $customer_id;
            return $customer;
        });

        $filteredClaims = collect($claims)
                ->only('id', 'user_id', 'date_format', 'time_format', 'timezone', 'language', 'scopes', 'is_direct',
                        'organization', 'permissions')
                ->filter(fn($value) => !is_null($value))
                ->toArray();

        if (empty($filteredClaims['scopes']) && !empty($filteredClaims['permissions'])) {
            $filteredClaims['scopes'] = $filteredClaims['permissions'];
        }

        $user_id = Arr::get($filteredClaims, 'user_id');
        $request_uri = $request->getRequestUri();
        $payload = $request->except(['password', 'password_confirmation', 'file']);
        $method = $request->method();
        $now = now()->toDateTimeString();

        if ($customer_id && $user_id) {
            AuditLogJob::dispatch($customer_id, $user_id, $request_uri, $payload, $method, $now);
        }

        $authenticatedCustomer = app('App\Models\Customer');
        $authenticatedCustomer->setRawAttributes($filteredClaims);
        $authenticatedCustomer->id = $customer_id;
        $authenticatedCustomer->exists = true;

        auth()->login($authenticatedCustomer);

        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/json');

        return $next($request);
    }
}