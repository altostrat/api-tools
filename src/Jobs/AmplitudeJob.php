<?php

namespace Mikrocloud\Mikrocloud\Jobs;

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

    public $user;

    public function __construct(string $eventType, $user)
    {
        $this->eventType = $eventType;
        $this->user = $user;
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
                    'user_id' => $this->user->user_id,
                    'user_properties' => [
                        'team_id' => $this->user->id,
                        'timezone' => $this->user->timezone,
                    ],
                ],
            ],
        ])->json();
    }
}
