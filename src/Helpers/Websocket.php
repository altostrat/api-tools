<?php

namespace Mikrocloud\Mikrocloud\Helpers;

use Mikrocloud\Mikrocloud\Events\FrontendPushEvent;

class Websocket
{
    public static function push($user_id, $event, $data = [])
    {
        event(new FrontendPushEvent($user_id, $event, $data));
    }
}