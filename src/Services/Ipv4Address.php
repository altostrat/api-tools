<?php

namespace Mikrocloud\Mikrocloud\Services;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Ipv4Address
{
    public $ip_address;

    public $prefix;

    public $network;

    public $first_host;

    public $last_host;

    public $broadcast;

    public $netmask;

    public $cidr = 32;

    public $ip_count;

    public $host_count;

    public $type;

    public $bogon = false;

    public $valid_prefix = true;

    public $country;

    public $country_code;

    public $region;

    public $region_name;

    public $city;

    public $zip;

    public $lat;

    public $lon;

    public $timezone;

    public $isp;

    public $org;

    public $as;

    public function __construct(string $rfc_4632_prefix)
    {
        $array = explode('/', $rfc_4632_prefix);

        $this->ip_address = $array[0];
        $this->cidr = (int)(isset($array[1]) ? $array[1] : 32);

        if (!filter_var($this->ip_address, FILTER_VALIDATE_IP)) {
            throw new Exception("{$this->ip_address} is not a valid IPv4 address");
        }

        if ($this->cidr < 0 || $this->cidr > 32) {
            throw new Exception("/{$this->cidr} is not a valid CIDR notation");
        }

        $this->network = long2ip((ip2long($this->ip_address)) & ((-1 << (32 - $this->cidr))));
        $this->broadcast = long2ip((ip2long($this->network)) + pow(2, (32 - $this->cidr)) - 1);
        $this->netmask = $this->cidr2subnet();
        $this->ip_count = 1 << (32 - $this->cidr);
        $this->host_count = $this->cidr == 31 ? 2 : ($this->cidr > 31 ? 1 : $this->ip_count - 2);
        $this->prefix = implode('/', [$this->network, $this->cidr]);
        $this->first_host = long2ip(ip2long($this->network) + ($this->cidr == 32 ? 0 : 1));
        $this->last_host = long2ip(ip2long($this->broadcast) - ($this->cidr == 32 ? 0 : 1));

        $this->calculate();

        return $this;
    }

    private function calculate(): self
    {
        $reserved_ranges = [
            '0.0.0.0/8' => 'bogon',
            '10.0.0.0/8' => 'rfc1918',
            '100.64.0.0/10' => 'cgnat',
            '127.0.0.0/8' => 'loopback',
            '169.254.0.0/16' => 'linklocal',
            '172.16.0.0/12' => 'rfc1918',
            '192.0.0.0/24' => 'ietf',
            '192.0.2.0/24' => 'testnet1',
            '192.88.99.0/24' => 'reserved',
            '192.168.0.0/16' => 'rfc1918',
            '198.18.0.0/15' => 'testing',
            '198.51.100.0/24' => 'testnet2',
            '203.0.113.0/24' => 'testnet3',
            '224.0.0.0/4' => 'multicast',
            '233.252.0.0/24' => 'mcasttestnet',
            '240.0.0.0/4' => 'reserved',
            '255.255.255.255/32' => 'broadcast',
        ];

        $types = [
            'public',
        ];

        foreach ($reserved_ranges as $prefix => $type) {
            $range = explode('/', $prefix);
            $ip = ip2long($range[0]);
            $cidr = $range[1];

            $network = ($ip) & ((-1 << (32 - $cidr)));
            $broadcast = $network + pow(2, (32 - $cidr)) - 1;

            $user_network = ip2long($this->network);
            $user_broadcast = ip2long($this->broadcast);

            if ($user_network < $broadcast && $user_broadcast > $network) {
                if ($user_network >= $network && $user_broadcast <= $broadcast) {
                    $types = [];
                }
                array_push($types, $type);
                $this->bogon = true;
            }
        }

        if (count($types) > 1) {
            $this->valid_prefix = false;
            $this->type = 'Overlapping';
        }

        if (count($types) == 1) {
            $this->type = $types[0];
        }

        return $this;
    }

    private function cidr2subnet(): string
    {
        $netmask = str_split(str_pad(str_pad('', $this->cidr, '1'), 32, '0'), 8);

        foreach ($netmask as &$element) {
            $element = bindec($element);
        }

        return implode('.', $netmask);
    }

    public function withIsp()
    {
        $cache_key = sha1("ip-api:{$this->ip_address}");

        $url = 'http://ip-api.com/json/' . urlencode($this->ip_address);

        if (!$response = Cache::get($cache_key)) {
            $response = Http::get($url)->json();
            Cache::put($cache_key, $response, 86400 * 7);
        }

        collect($response)->each(function ($value, $key) {
            $property = Str($key)->snake()->toString();

            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        });

        preg_match('/AS(\d+)/', $this->as, $matches);
        $this->as = Arr::get($matches, 1);

        return $this;
    }
}
