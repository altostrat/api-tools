<?php

namespace Altostrat\Tools\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class AmplitudeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $eventType;

    public string $user_id;

    public string $team_id;

    public string $timezone;

    public function __construct(string $eventType, string $user_id, string $team_id, string $timezone)
    {
        $this->eventType = $eventType;
        $this->user_id = $user_id;
        $this->team_id = $team_id;
        $this->timezone = $timezone;
    }

    public function handle()
    {
        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post(config('altostrat.amplitude.endpoint'), [
            'api_key' => config('altostrat.amplitude.api_key'),
            'events' => [
                [
                    'event_type' => $this->eventType,
                    'user_id' => $this->user_id,
                    'user_properties' => [
                        'team_id' => $this->team_id,
                        'timezone' => $this->timezone,
                    ],
                ],
            ],
        ])->json();
    }
}
