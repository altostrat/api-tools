<?php

namespace Altostrat\Tools\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IpLookup
{
    /**
     * Get information about an IP address.
     *
     * @param  string  $ip_address
     * @return object
     */
    public static function info($ip_address)
    {
        // Generate a cache key based on the provided IP address
        $cache_key = sha1("ip-api:{$ip_address}");

        // If information for the given IP address is found in the cache, return it
        if ($cache = Cache::get($cache_key)) {
            return json_decode($cache);
        }

        // Prepare the URL to query for the IP information
        $url = 'http://ip-api.com/json/'.urlencode($ip_address);

        // Send a GET request to the IP information API and retrieve the response body
        $response = Http::get($url)->body()->throw();

        // Cache the returned IP information data with a TTL of 7 days
        Cache::put($cache_key, $response, 86400 * 7);

        // Return the decoded JSON response body containing the IP information
        return json_decode($response);
    }
}
