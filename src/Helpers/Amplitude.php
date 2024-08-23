<?php

namespace Mikrocloud\Mikrocloud\Helpers;

use Illuminate\Support\Facades\Http;

class Amplitude
{
    public static function report(string $eventType)
    {
        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(config('mikrocloud.amplitude.endpoint'), [
            'api_key' => config('mikrocloud.amplitude.api_key'),
            'events' => [
                [
                    'event_type' => $eventType,
                    'user_id' => auth()->user()->user_id,
                    'user_properties' => [
                        'team_id' => auth()->user()->id,
                        'timezone' => auth()->user()->timezone,
                    ],
                ],
            ],
        ])->json();
    }
}
