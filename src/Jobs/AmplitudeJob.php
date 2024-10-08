<?php

namespace Mikrocloud\Mikrocloud\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AmplitudeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $eventType;

    public function __construct(string $eventType)
    {
        $this->eventType = $eventType;
    }

    public function handle()
    {
        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(config('mikrocloud.amplitude.endpoint'), [
            'api_key' => config('mikrocloud.amplitude.api_key'),
            'events' => [
                [
                    'event_type' => $this->eventType,
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
