<?php

namespace Altostrat\Tools\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Aws\Sns\SnsClient;
use Aws\Exception\AwsException;
use Tuupola\Ksuid;

class PublishAuditLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $logData;

    public function __construct(array $logData)
    {
        $this->logData = $logData;
    }

    public function handle(): void
    {
        $dsn = config('altostrat.logging.audit_log_dsn');

        if (empty($dsn)) {
            // DSN not set, do nothing.
            return;
        }

        try {
            // 1. Parse the DSN
            preg_match('/^(?P<key>[^:]+):(?P<secret>[^@]+)@(?P<arn>.+)$/', $dsn, $matches);

            if (empty($matches['key']) || empty($matches['secret']) || empty($matches['arn'])) {
                Log::error('Invalid AUDIT_LOG_DSN format.', ['dsn' => $dsn]);
                return;
            }

            // 2. Prepare the payload for the audit log service
            $ksuid = new Ksuid();
            $payload = [
                'log_id'         => 'log_' . $ksuid->string(),
                'request_id'     => 'req_' . $ksuid->string(), // Generate a unique request ID
                'org_id'         => $this->logData['org_id'],
                'workspace_id'   => $this->logData['workspace_id'],
                'user_id'        => $this->logData['user_id'],
                'session_id'     => null, // Can be added later if available
                'event_time'     => now()->toISOString(),
                'http_verb'      => $this->logData['method'],
                'endpoint'       => $this->logData['uri'],
                'status_code'    => $this->logData['status_code'],
                'ip_address'     => $this->logData['ip'],
                'user_agent'     => $this->logData['user_agent'],
                'frontend_page'  => $this->logData['frontend_page'],
                'request_payload' => $this->logData['request_payload'],
                'response_payload' => $this->logData['response_payload'],
            ];

            // 3. Create SNS Client
            $snsClient = new SnsClient([
                'version' => 'latest',
                'region'  => explode(':', $matches['arn'])[3], // Extract region from ARN
                'credentials' => [
                    'key'    => $matches['key'],
                    'secret' => $matches['secret'],
                ],
            ]);

            // 4. Publish to SNS
            $snsClient->publish([
                'TopicArn' => $matches['arn'],
                'Message'  => json_encode($payload),
            ]);

        } catch (AwsException $e) {
            Log::error('Failed to publish audit log to SNS.', [
                'error' => $e->getAwsErrorMessage(),
                'request_id' => $payload['request_id'] ?? 'unknown',
            ]);
            // Optionally re-throw to trigger job retries
            $this->fail($e);
        } catch (\Exception $e) {
            Log::error('Generic error in PublishAuditLog job.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->fail($e);
        }
    }
}
