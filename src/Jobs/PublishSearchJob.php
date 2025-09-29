<?php

namespace Altostrat\Tools\Jobs;

use Aws\Sns\SnsClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PublishSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $eventData;

    public function __construct(array $eventData)
    {
        $this->eventData = $eventData;
    }

    public function handle(): void
    {
        $dsn = config('altostrat.search_dsn');

        if (empty($dsn)) {
            // DSN not set, do nothing. This is the intended way to disable it.
            return;
        }

        try {
            // 1. Parse the DSN
            $parsed = $this->parseDsn($dsn);

            if ($parsed === null) {
                Log::error('Invalid SEARCH_DSN format.', ['dsn' => $dsn]);

                return;
            }

            /** @var \Aws\Sns\SnsClient $snsClient */
            $snsClient = new SnsClient([
                'version' => 'latest',
                'region' => $parsed['region'],
                'credentials' => [
                    'key' => $parsed['key'],
                    'secret' => $parsed['secret'],
                ],
            ]);

            // 3. Publish to SNS
            $snsClient->publish([
                'TopicArn' => $parsed['arn'],
                'Message' => json_encode($this->eventData),
            ]);

            Log::info('Successfully published search event to SNS.', [
                'topic_arn' => $parsed['arn'],
                'event_type' => $this->eventData['event_type'],
                'id' => $this->eventData['payload']['id'],
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to publish search event to SNS.', [
                'error' => $e->getMessage(),
                'dsn' => $dsn, // Log the DSN for debugging
            ]);

            // Re-throw to let the queue system handle retries
            throw $e;
        }
    }

    /**
     * Parses the DSN string into its components.
     *
     * @return array{key: string, secret: string, arn: string, region: string}|null
     */
    private function parseDsn(string $dsn): ?array
    {
        // DSN format: key:secret@arn:aws:sns:region:account-id:topic-name
        $pattern = '/^(?P<key>[^:]+):(?P<secret>[^@]+)@(?P<arn>arn:aws:sns:(?P<region>[^:]+):.+)$/';

        if (! preg_match($pattern, $dsn, $matches)) {
            return null;
        }

        return [
            'key' => $matches['key'],
            'secret' => $matches['secret'],
            'arn' => $matches['arn'],
            'region' => $matches['region'],
        ];
    }
}
