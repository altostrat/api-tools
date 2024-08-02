<?php

namespace Mikrocloud\Mikrocloud\Http\Middleware;

use App\Models\Customer;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Mikrocloud\Mikrocloud\Jobs\SnsAuditLogJob;

class Auth0Users
{
    protected function getClient(): SdkConfiguration
    {
        $env = config('app.env');
        $audience = $env === 'staging' ? 'https://api.staging.mikrocloud.com' : 'https://api.mikrocloud.com';

        return new SdkConfiguration([
            'domain' => config('mikrocloud.auth0.domain'),
            'clientId' => config('mikrocloud.auth0.client_id'),
            'cookieSecret' => config('mikrocloud.auth0.cookie_secret'),
            'audience' => [$audience],
        ]);
    }

    public function handle(Request $request, Closure $next)
    {
        $bearerToken = $request->bearerToken() ?? '';

        try {
            $client = $this->getClient();
            $token = new Token($client, $bearerToken);
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
            ->only('id', 'user_id', 'date_format', 'time_format', 'timezone', 'language', 'scopes', 'is_direct')
            ->filter(function ($value) {
                return ! is_null($value);
            })->toArray();

        $user_id = Arr::get($claims, 'user_id');
        $request_uri = $request->getRequestUri();
        $payload = $request->all();
        $method = $request->method();
        $now = now()->toDateTimeString();
        SnsAuditLogJob::dispatch($customer_id, $user_id, $request_uri, $payload, $method, $now);

        auth()->login(app('App\Models\Customer')->setRawAttributes($claims));

        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/json');

        return $next($request);
    }
}
