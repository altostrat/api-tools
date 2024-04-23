<?php

namespace Mikrocloud\Mikrocloud\Traits;

use Illuminate\Support\Carbon;

trait HasCarbonDates
{

    public function carbonFormat($date)
    {
        if (is_null($date)) {
            return null;
        }
        
        $timezone = auth()->user()?->timezone ?? 'UTC';
        $date_format = auth()->user()?->date_format ?? 'd M Y';
        $time_format = auth()->user()?->time_format ?? 'H:i:s';

        return $date->timezone($timezone)->format("$date_format $time_format");
    }
}
