<?php

namespace Altostrat\Tools\Helpers;

use Illuminate\Support\Facades\Http;
use Altostrat\Tools\Jobs\AmplitudeJob;

class Amplitude
{
    public static function report(string $eventType)
    {
        //call the amplitude job
        if (!auth()->user()->user_id) {
            //auth mikroservice
            AmplitudeJob::dispatch($eventType,auth()->user()->id,auth()->user()->current_team_id,auth()->user()->timezone);
        } else {
            AmplitudeJob::dispatch($eventType,auth()->user()->user_id,auth()->user()->id,auth()->user()->timezone);
        }
    }
}
