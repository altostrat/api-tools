<?php

namespace Mikrocloud\Mikrocloud\Helpers;

use Illuminate\Support\Facades\Http;
use Mikrocloud\Mikrocloud\Jobs\AmplitudeJob;

class Amplitude
{
    public static function report(string $eventType)
    {
        //call the amplitude job
        AmplitudeJob::dispatch($eventType,auth()->user());
    }
}
