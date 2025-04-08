<?php

namespace Altostrat\Tools\Helpers;

use Altostrat\Tools\Events\FrontendPushEvent;

class Websocket
{
    public static function push($user_id, $event, $data = [])
    {
        event(new FrontendPushEvent($user_id, $event, $data));
    }
}
