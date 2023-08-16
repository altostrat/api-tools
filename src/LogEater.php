<?php

namespace Mikrocloud\Mikrocloud;

use Illuminate\Support\Facades\Http;
use Psr\Log\LoggerInterface;

class LogEater implements LoggerInterface
{
    protected $level;

    public function __construct($level)
    {
        $this->level = $level;
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->sendLog($message, $context);
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->sendLog($message, $context);
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->sendLog($message, $context);
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->sendLog($message, $context);
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->sendLog($message, $context);
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->sendLog($message, $context);
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->sendLog($message, $context);
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->sendLog($message, $context);
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->level = $level;
        $this->sendLog($message, $context);
    }

    public function sendLog($message, $context)
    {
        $data = [
            'level' => $this->level,
            'message' => $message,
            'context' => $context,
            'enviroment' => config('app.env'),
        ];

        try {
            $result = Http::withToken(config('mikrocloud.key'))
                ->post(config('mikrocloud.url').config('mikrocloud.logging.endpoint'), $data);

            if ($result->status() === 401) {
                throw new \Exception('LogEater: Invalid MikroCloud key! Set MIKROCLOUD_KEY in your .env file');
            }

            if ($result->failed()) {
                throw new \Exception('LogEater: Failed to send log to MikroCloud');
            }

        } catch (\Throwable $th) {
            if (config('app.env') !== 'production') {
                throw $th;
            }
        }
    }
}
