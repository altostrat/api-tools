<?php

namespace Mikrocloud\Mikrocloud\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SnsAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $team_id,
        public string $user_id,
        public string $request_uri,
        public array $payload,
        public string $method,
        public string $now,
    )
    {
    }

    public function handle(): void
    {
        $sns = app('sns');

        $message = json_encode([ //send a sns message to audit topic
            'team_id' => $this->team_id,
            'user_id' => $this->user_id,
            'route' => $this->request_uri,
            'request' => $this->payload,
            'method' => $this->method,
            'now' => $this->now,
        ]);
        $sns->publish([
            'TopicArn' => 'arn:aws:sns:us-east-1:677655038339:audit',
            'Message' => $message,
        ]);
    }
}
