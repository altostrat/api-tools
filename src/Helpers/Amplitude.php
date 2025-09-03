<?php

namespace Altostrat\Tools\Helpers;

use Altostrat\Tools\Jobs\AmplitudeJob;

class Amplitude
{
    public static function report(string $eventType)
    {
        // call the amplitude job
        AmplitudeJob::dispatch($eventType, auth()->user()->sub, auth()->user()->org_id, auth()->user()->timezone);
    }
}
