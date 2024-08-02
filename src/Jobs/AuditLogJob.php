<?php

namespace Mikrocloud\Mikrocloud\Jobs;

use Aws\CloudWatchLogs\Exception\CloudWatchLogsException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AuditLogJob implements ShouldQueue
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
        $client = app('cloudwatch-logs');
        $message = json_encode([ //send a sns message to audit topic
            'team_id' => $this->team_id,
            'user_id' => $this->user_id,
            'route' => $this->request_uri,
            'request' => $this->payload,
            'method' => $this->method,
            'now' => $this->now,
        ]);
        try {

            $client->putLogEvents([
                'logGroupName' => 'AuditLog',
                'logStreamName' => $this->team_id,
                'logEvents' => [
                    [
                        'timestamp' => now()->timestamp * 1000,
                        'message' => $message,
                    ],
                ],
            ]);
        } catch (CloudWatchLogsException $e) {
            if ($e->getAwsErrorCode() == 'ResourceNotFoundException') {
                $this->createLogStream();
                $this->handle();
            }
        }
    }

    public function createLogStream(): void
    {
        $client = app('cloudwatch-logs');
        $client->createLogStream([
            'logGroupName' => 'AuditLog',
            'logStreamName' => $this->team_id,
        ]);
    }
}
