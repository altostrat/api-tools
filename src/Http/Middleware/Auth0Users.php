<?php

namespace Altostrat\Tools\Http\Middleware;

use Altostrat\Tools\Jobs\PublishAuditLog;
use App\Models\Customer;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Auth0Users
{
    protected function getClient(bool $useLegacy = false): SdkConfiguration
    {
        $env = config('app.env');
        $audience = $env === 'staging' ? 'https://api.staging.altostrat.io' : 'https://api.altostrat.io';

        return new SdkConfiguration([
            'domain' => $useLegacy ? config('altostrat.auth0.legacy.domain') : config('altostrat.auth0.domain'),
            'clientId' => $useLegacy ? config('altostrat.auth0.legacy.client_id') : config('altostrat.auth0.client_id'),
            'cookieSecret' => config('altostrat.auth0.cookie_secret'),
            'audience' => [$audience],
        ]);
    }

    public function handle(Request $request, Closure $next)
    {
        // This new method now contains all the logic to get a JWT,
        // whether it's passed directly or exchanged from a static key.
        $jwt = $this->getJwtFromRequest($request);

        if (! $jwt) {
            return response()->json(['message' => 'Unauthorized or invalid token provided.'], 401);
        }

        try {
            $client = $this->getClient();
            $token = new Token($client, $jwt);
            $token->validate();
            $claims = $token->toArray();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        $customer_id = Arr::get($claims, 'id');
        abort_unless($customer_id, 401, 'Customer ID not found in token');

        app()->bind('App\Models\Customer', function () use ($customer_id) {
            return new Customer($customer_id);
        });

        $claims = collect($claims)
            ->only(
                'id',
                'user_id',
                'urn:altostrat:email',
                'urn:altostrat:name',
                'date_format',
                'time_format',
                'timezone',
                'language',
                'scopes',
                'is_direct',
                'email',
                'permissions',
                'organization',
                'sub',
                'org_id',
                'workspace_id',
                'session',
                'name'
            )
            ->filter(function ($value) {
                return ! is_null($value);
            })->toArray();

        if (! isset($claims['scopes'])) {
            $claims['scopes'] = $claims['permissions'];
            unset($claims['permissions']);
        }

        auth()->login(app('App\Models\Customer')->setRawAttributes($claims));

        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/json');

        // Let the request complete
        $response = $next($request);

        if (config('altostrat.logging.audit_log_dsn')) {
            $this->dispatchAuditLog($request, $response, $claims);
        }

        return $response;
    }

    /**
     * Retrieves a valid JWT from the request, exchanging a static API key if necessary.
     */
    protected function getJwtFromRequest(Request $request): ?string
    {
        $bearerToken = $request->bearerToken() ?? '';
        if (empty($bearerToken)) {
            return null;
        }

        // If the token is a static key, exchange it for a JWT.
        if (str_starts_with($bearerToken, 'alto_sk_')) {
            return $this->exchangeStaticKeyForJwt($bearerToken);
        }

        // Otherwise, assume it's already a JWT.
        return $bearerToken;
    }

    /**
     * Exchanges a static key for a JWT, using caching to improve performance.
     */
    protected function exchangeStaticKeyForJwt(string $staticKey): ?string
    {
        $cacheKey = 'jwt_for_static_key:'.hash('sha256', $staticKey);

        // Return from cache if available
        $cachedJwt = Cache::get($cacheKey);
        if ($cachedJwt) {
            return Crypt::decryptString($cachedJwt);
        }

        try {
            $response = Http::withToken(config('altostrat.apikeys.internal_auth_token'))
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(config('altostrat.apikeys.token_endpoint'), [
                    'api_key' => $staticKey,
                ])
                ->throw() // Throw exception on 4xx/5xx errors
                ->json();

            $accessToken = Arr::get($response, 'data.access_token');
            $expiresIn = Arr::get($response, 'data.expires_in', 3600);

            if (! $accessToken) {
                Log::error('Token exchange response did not contain an access_token.');

                return null;
            }

            // Cache the encrypted token for its validity period minus a 60-second buffer.
            $cacheTtl = max(60, $expiresIn - 60);
            Cache::put($cacheKey, Crypt::encryptString($accessToken), $cacheTtl);

            return $accessToken;

        } catch (\Exception $e) {
            Log::error('Failed to exchange static API key for JWT.', [
                'error' => $e->getMessage(),
                'static_key_prefix' => substr($staticKey, 0, 13),
            ]);

            return null;
        }
    }

    private function getTokenIss(string $token)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode($parts[1]), true);
        if (isset($payload['iss'])) {
            return $payload['iss'];
        }

        return null;
    }

    /**
     * Gathers data and dispatches the audit log job.
     */
    protected function dispatchAuditLog(Request $request, Response $response, array $claims): void
    {
        // skip if method is get
        if ($request->method() === 'GET') {
            return;
        }
        try {
            $payload = $request->all();
            if ($request->files->count() > 0) {
                $payload = collect($payload)->except('file')->toArray();
            }

            // Capture response only if it's an error
            $responsePayload = null;
            $statusCode = $response->getStatusCode();
            if ($statusCode < 200 || $statusCode >= 400) {
                // Try to decode json, otherwise get raw content
                $responseContent = json_decode($response->getContent(), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $responseContent = $response->getContent();
                }
                $responsePayload = $responseContent;
            }
            $ip = $request->header('x-forwarded-for') ?: $request->ip();

            $logData = [
                'email' => Arr::get($claims, 'email') ?? Arr::get($claims, 'urn:altostrat:email'),
                'name' => Arr::get($claims, 'name') ?? Arr::get($claims, 'urn:altostrat:name'),
                'user_id' => Arr::get($claims, 'sub'),
                'org_id' => Arr::get($claims, 'org_id'),
                'workspace_id' => Arr::get($claims, 'workspace_id'),
                'uri' => $request->getRequestUri(),
                'request_payload' => $payload,
                'method' => $request->method(),
                'status_code' => $statusCode,
                'response_payload' => $responsePayload,
                'ip' => $ip,
                'session_id' => Arr::get($claims, 'session'),
                'user_agent' => $request->userAgent(),
                'frontend_page' => $request->header('x-current-url'),
            ];

            PublishAuditLog::dispatch($logData);
        } catch (\Exception $e) {
            // Log a failure to dispatch, but don't break the user's request
            Log::error('Failed to dispatch audit log job.', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
