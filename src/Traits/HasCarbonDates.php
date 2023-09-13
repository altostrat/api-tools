<?php

namespace Mikrocloud\Mikrocloud\Traits;

trait HasCarbonDates
{

    public function carbonFormat($date)
    {
        $timezone = auth()->user()?->timezone ?? 'UTC';
        $date_format = auth()->user()?->date_format ?? 'd M Y';
        $time_format = auth()->user()?->time_format ?? 'H:i:s';

        return $date->timezone($timezone)->format("$date_format $time_format");
    }
}
